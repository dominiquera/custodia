<?php

namespace Custodia\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PragmaRX\ZipCode\Contracts\ZipCode;
use Naughtonium\LaravelDarkSky\Facades\DarkSky;
use Custodia\UserProfile;
use Custodia\WeatherForecast;

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
    const CACHE_TIME = 1;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update weather forecasts';
    
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
    public function __construct(ZipCode $zipcode)
    {
        parent::__construct();
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
        
        Log::info("Updating weather forecasts");
        $this->updateWeatherForecasts();
    }
    
    /**
     * Attempt to find & fill missing city information
     */
    public function findMissingCities() {
        // find all user profiles with zip but without city
        $locations = UserProfile::all()
                ->unique('zip')
                ->where('city', null)
                ->where('zip', '!=', null);

        foreach ($locations as $location) {
            $zip = Cache::remember(
                'findMissingCities_CA_' . $location->zip,
                self::CACHE_TIME,
                function() use ($location) {
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
            } else {
                Log::warning("Unable to resolve location information for zip: " . $location->zip);
            }
        }
    }
    
    /**
     * Get the weather forecast for a location
     * 
     * @param array $location
     * @return array
     */
    public function getWeatherForecast($location) {
        $result = Cache::remember(
            // NOTE end of cache key signifies included dataset(s)
            sprintf('getWeatherForecast_CA_%0.6f_%0.6f:C+D+A',
                    $location->latitude, $location->longitude),
            self::CACHE_TIME,
            function() use ($location) {
                return DarkSky::location(
                    $location->latitude, 
                    $location->longitude)
                        // NOTE if changing includes also change the cache key
                        ->includes(['currently', 'daily', 'alerts'])
                        ->units('ca')
                        ->get();
            });

        return $result;
    }
    
    /**
     * Update current weather forecast and alerts for known locations
     */
    public function updateWeatherForecasts() {
        $locations = UserProfile::all()
                ->unique('longitude','latitude')
                ->where('longitude', '!=', null)
                ->where('latitude', '!=', null);
        
        foreach ($locations as $location) {
            $result = $this->getWeatherForecast($location);
            $date = date('Y-m-d', $result->currently->time);

            if ($this->validateWeatherForecastResult($result)) {
                $weatherForecast = WeatherForecast::firstOrNew([
                        'for_date' => $date,
                        'city' => $location->city,
                        'state' => $location->state
                ]);
                
                $weatherForecast->for_date = $date;
                $weatherForecast->city = $location->city;
                $weatherForecast->state = $location->state;
                $weatherForecast->raw = $result;
            
                $weatherForecast->save();
            }
        }
    }
    
    public function validateWeatherForecastResult($result) {
        return !empty($result) && 
            !empty($result->currently);
    }
    
    /**
     * Validates the data returned from ZipCode::find()
     * 
     * @param array $result
     * @return bool
     */
    public function validateZipCodeFindResult($result) {
        return !(empty($result) ||
            empty($result['result_raw']) ||
            empty($result['result_raw']['standard']) ||
            empty($result['result_raw']['standard']['city']) ||
            empty($result['result_raw']['standard']['prov']));
    }
}
