<?php

namespace Custodia\Services;

use Custodia\Tools\Evaluator;
use Custodia\User;
use Custodia\UserProfile;
use Custodia\WeatherForecast;
use Custodia\WeatherTriggerType;
use Illuminate\Support\Facades\Log;

class WeatherTriggerService
{
    /**
     * @var WeatherForecastService
     */
    private $weatherForecastService;

    public function __construct(WeatherForecastService $weatherForecastService) {
        $this->weatherForecastService = $weatherForecastService;
    }

    public function checkWeatherTrigger(WeatherTriggerType $weatherTriggerType, User $user)
    {
        // get user location
        $location = $this->getUserLocation($user);

        // test rule against weather for location
        return $this->testRuleAgainstWeatherForLocation($weatherTriggerType->rule, $location);
    }

    private function getUserLocation(User $user)
    {
        return [
            'city' => $user->userProfile->city,
            'state' => $user->userProfile->state,
            'zip' => $user->userProfile->zip,
            'longitude' => $user->userProfile->longitude,
            'latitude' => $user->userProfile->latitude,
        ];
    }

    private function testRuleAgainstWeatherForLocation($rule, $location)
    {
        $evaluator = $this->getRuleEvaluator($rule);
        $arguments = $this->getRuleArguments($location);

        if (!$arguments)
            return false;

        return $evaluator->evaluate($arguments);
    }

    private function getRuleEvaluator($expression)
    {
        return new Evaluator($expression);
    }

    private function getRuleArguments($location): array
    {
        $daily_forecast = $this->weatherForecastService->getDailyForecast($location['city'], $location['state']);
        $two_day_results = $this->weatherForecastService->getPastResults($location['city'], $location['state']);

        if (empty($daily_forecast)) {
            Log::error("unable to test weather trigger rule; no daily weather forecast", $location);
            return false;
        }

        $arguments = [
            '$last_snow' => PHP_INT_MAX, // infinity
            '$last_rain' => PHP_INT_MAX, // infinity
            '$last_temp_low' => PHP_INT_MAX, // error
            '$last_temp_high' => PHP_INT_MIN, // error
            '$last_snow_accum' => 0,
            '$last_rain_accum' => 0,
            '$today_rain' => 0,
            '$today_snow' => 0,
            '$today_temp' => $daily_forecast['temperatureHigh'],
        ];

        if ($daily_forecast['precipIntensity'] > 0) {
            switch ($daily_forecast['precipType']) {
                case "rain":
                    $arguments['$today_rain'] = $daily_forecast['precipIntensity'] * 24;
                    break;

                case "snow":
                    $arguments['$today_snow'] = $daily_forecast['precipIntensity'] * 24;
                    break;

                case "sleet":
                    // TODO determine if this counts as rain or snow
                    break;
            }
        }

        if ($two_day_results) {
            $temp_min = PHP_INT_MAX; // error
            $temp_max = PHP_INT_MIN; // error
            $rain_last = 0;
            $snow_last = 0;
            $rain_accum = 0;
            $snow_accum = 0;

            foreach ($two_day_results as $hourly_result) {
                if ($hourly_result->raw['currently']['temperature'] < $temp_min)
                    $temp_min = $hourly_result->raw['currently']['temperature'];

                if ($hourly_result->raw['currently']['temperature'] > $temp_max)
                    $temp_max = $hourly_result->raw['currently']['temperature'];

                if (isset($hourly_result->raw['currently']['precipProbability']) &&
                    isset($hourly_result->raw['currently']['precipType'])) {
                    if ($hourly_result->raw['currently']['precipProbability'] > 0) {
                        switch ($hourly_result->raw['currently']['precipType']) {
                            case "rain":
                                if (isset($hourly_result->raw['currently']['precipAccumulation'])) {
                                    $rain_accum += $hourly_result->raw['currently']['precipAccumulation'];

                                    if ($hourly_result->raw['currently']['time'] > $rain_last)
                                        $rain_last = $hourly_result->raw['currently']['time'];
                                }
                                break;

                            case "snow":
                                if (isset($hourly_result->raw['currently']['precipAccumulation'])) {
                                    $snow_accum += $hourly_result->raw['currently']['precipAccumulation'];

                                    if ($hourly_result->raw['currently']['time'] > $snow_last)
                                        $snow_last = $hourly_result->raw['currently']['time'];
                                }
                                break;

                            case "sleet":
                                // TODO determine if this counts as rain or snow
                                break;
                        }
                    }
                }
            }

            $arguments['$last_temp_low'] = $temp_min;
            $arguments['$last_temp_high'] = $temp_max;

            $now = time();
            // convert last timestamps into relative offsets in hours, "hours since last event"
            if ($rain_last > 0)
                $arguments['$last_rain'] = ($now - $rain_last) / 3600;
            else
                $arguments['$last_rain'] = PHP_INT_MAX; // infinity

            if ($snow_last > 0)
                $arguments['$last_snow'] = ($now - $snow_last) / 3600;
            else
                $arguments['$last_snow'] = PHP_INT_MAX; // infinity

            $arguments['$last_rain_accum'] = $rain_accum;
            $arguments['$last_snow_accum'] = $snow_accum;
        }

        return $arguments;
    }
}