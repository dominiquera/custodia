<?php

namespace Custodia\Console\Commands;

use Custodia\Services\UserService;
use Custodia\Services\WeatherForecastService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PragmaRX\ZipCode\Contracts\ZipCode;
use Naughtonium\LaravelDarkSky\Facades\DarkSky;
use Custodia\UserProfile;

/**
 * Weather forecast update process:
 *
 *  - Obtains current temperature and expected snowfall for known locations.
 *
 */
class WeatherUpdate extends Command
{
    /**
     * The cache timeout (in seconds)
     */
    const CACHE_TIME = 1800;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:update {--backfill= : Number of days to backfill}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weather forecasts';

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var WeatherForecastService
     */
    private $weatherForecastService;

    /**
     * ZipCode lookup service
     *
     * @var ZipCode
     */
    private $zipcode;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WeatherForecastService $weatherForecastService, UserService $userService, ZipCode $zipcode)
    {
        parent::__construct();
        $this->weatherForecastService = $weatherForecastService;
        $this->userService = $userService;
        $this->zipcode = $zipcode;

        // TODO support for other countries
        $this->zipcode->setCountry('CA');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Finding missing city information");
        $this->findMissingCities();

        $backfill = $this->option('backfill');

        if ($backfill) {
            Log::info("Updating historical weather forecasts for $backfill days");
            $this->updateHistoricalForecasts($backfill);
        }

        Log::info("Updating weather forecasts");
        $this->updateWeatherForecasts();
    }

    /**
     * Attempt to find & fill missing city information
     *
     * @return void
     */
    public function findMissingCities()
    {
        // find all user profiles with zip but without city
        $locations = UserProfile::all()
                                ->unique('zip')
                                ->where('city', null)
                                ->where('zip', '!=', null);

        foreach ($locations as $location) {
            $zip = Cache::remember(
                'findMissingCities_CA_' . $location->zip,
                self::CACHE_TIME,
                function () use ($location) {
                    return $this->zipcode->find($location->zip);
                });

            if ($zip && $this->validateZipCodeFindResult($zip)) {
                // use the first address from the result if present
                UserProfile::where('zip', $location->zip)
                           ->where('city', null)
                           ->update([
                                        'city' => $zip['result_raw']['standard']['city'],
                                        'state' => $zip['result_raw']['standard']['prov'],
                                        'longitude' => $zip['result_raw']['longt'],
                                        'latitude' => $zip['result_raw']['latt'],
                                    ]);
            }
            else {
                Log::warning("Unable to resolve location information for zip: " . $location->zip);
            }
        }
    }

    /**
     * Get the historical weather data for a location
     *
     * @param $location
     * @param $time
     * @return mixed
     */
    public function getHistoricalWeatherForecast($location, $time)
    {
        $result = Cache::remember(
        // NOTE end of cache key signifies included dataset(s)
            sprintf('getHistoricalWeatherForecast_CA_%0.6f_%0.6f_%d:C+A',
                    $location->latitude, $location->longitude, $time),
            self::CACHE_TIME,
            function () use ($location, $time) {
                return DarkSky::location(
                    $location->latitude,
                    $location->longitude)
                    // NOTE if changing includes also change the cache key
                              ->includes([ 'currently', 'alerts' ])
                              ->units('ca')
                              ->atTime($time)
                              ->get();
            });

        return $result;
    }

    /**
     * Get the weather forecast for a location
     *
     * @param array $location
     * @return mixed
     */
    public function getWeatherForecast($location)
    {
        $result = Cache::remember(
        // NOTE end of cache key signifies included dataset(s)
            sprintf('getWeatherForecast_CA_%0.6f_%0.6f:C+D+A',
                    $location->latitude, $location->longitude),
            self::CACHE_TIME,
            function () use ($location) {
                return DarkSky::location(
                    $location->latitude,
                    $location->longitude)
                    // NOTE if changing includes also change the cache key
                              ->includes([ 'currently', 'daily', 'alerts' ])
                              ->units('ca')
                              ->get();
            });

        return $result;
    }

    /**
     * Update historical weather forecast and alerts for known locations.
     *
     * @param int $days number of days to backfill
     * @return void
     */
    public function updateHistoricalForecasts($days = 2)
    {
        $locations = $this->userService->getUserLocations();

        // time to backfill until; excludes the current hour
        $now = time();
        $now = $now - ($now % 3600);

        foreach ($locations as $profile) {
            // start at the beginning of the Nth prior day, one iteration per hour
            for ($time = $now - ($now % 86400) - (86400 * $days); $time < $now; $time += 3600) {
                $date = date('Y-m-d', $time);
                $hour = date('H', $time);

                // only backfill the missing hours
                if ($this->weatherForecastService->hasForecast($profile->city, $profile->state, $date, $hour))
                    continue;

                Log::info("Fetching forecast for $profile->city, $profile->state on $date at $hour:00");

                $result = $this->getHistoricalWeatherForecast($profile, $time);

                if (!$this->validateWeatherForecastResult($result)) {
                    Log::error("weather forecast result did not validate", (array)$result);
                    continue;
                }

                $this->weatherForecastService->saveForecast($profile->city, $profile->state, $date, $hour, $result);
            }
        }
    }

    /**
     * Update current weather forecast and alerts for known locations\
     *
     * @return void
     */
    public function updateWeatherForecasts()
    {
        $locations = $this->userService->getUserLocations();

        foreach ($locations as $profile) {
            $result = $this->getWeatherForecast($profile);
            $date = date('Y-m-d', $result->currently->time);
            $hour = date('H', $result->currently->time);

            if ($this->validateWeatherForecastResult($result)) {
                $this->weatherForecastService->saveForecast($profile->city, $profile->state, $date, $hour, $result);
            }
            else {
                Log::error("weather forecast result did not validate", (array)$result);
            }
        }
    }

    /**
     * Validate the weather forecast result
     *
     * @param $result
     * @return bool
     */
    public function validateWeatherForecastResult($result) : bool
    {
        // at least make sure the current forecast is present
        // TODO further validate this, translate to an app-specific DTO
        return !empty($result) &&
            !empty($result->currently);
    }

    /**
     * Validate the data returned from ZipCode::find()
     *
     * @param array $result
     * @return bool
     */
    public function validateZipCodeFindResult($result) : bool
    {
        return !(empty($result) ||
            empty($result['result_raw']) ||
            empty($result['result_raw']['standard']) ||
            empty($result['result_raw']['standard']['city']) ||
            empty($result['result_raw']['standard']['prov']));
    }
}
