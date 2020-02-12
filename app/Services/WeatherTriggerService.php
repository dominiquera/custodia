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
        $results_last = $this->weatherForecastService->getPastResults($location['city'], $location['state']);
        $results_next = $this->weatherForecastService->getFutureResults($location['city'], $location['state']);
        $results_today = $this->weatherForecastService->getTodayResults($location['city'], $location['state']);

        if (empty($results_today)) {
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
            '$next_snow' => PHP_INT_MAX, // infinity
            '$next_rain' => PHP_INT_MAX, // infinity
            '$next_snow_accum' => 0,
            '$next_rain_accum' => 0,
            '$today_rain' => 0,
            '$today_snow' => 0,
            '$today_temp' => $results_today['temperatureHigh'],
        ];

        $now = time();
        $now_hour = $now - ($now % 3600); // round to nearest hour to keep offsets whole

        if ($results_today['precipIntensity'] > 0) {
            switch ($results_today['precipType']) {
                case "rain":
                    $arguments['$today_rain'] = $results_today['precipIntensity'] * 24;
                    break;

                case "snow":
                    $arguments['$today_snow'] = $results_today['precipIntensity'] * 24;
                    break;

                case "sleet":
                    // TODO determine if this counts as rain or snow
                    break;
            }
        }

        if ($results_next) {
            $rain_next = PHP_INT_MAX;
            $snow_next = PHP_INT_MAX;
            $rain_accum = 0;
            $snow_accum = 0;

            foreach ($results_next->raw['daily']['data'] as $daily_result) {
                if (isset($daily_result->raw['precipProbability']) &&
                    isset($daily_result->raw['precipType'])) {
                    if ($daily_result->raw['precipProbability'] > 0) {
                        switch ($daily_result->raw['precipType']) {
                            case "rain":
                                if (isset($daily_result->raw['precipAccumulation'])) {
                                    $rain_accum += $daily_result->raw['precipAccumulation'];

                                    if ($daily_result->raw['time'] < $rain_next)
                                        $rain_next = $daily_result->raw['time'];
                                }
                                break;

                            case "snow":
                                if (isset($daily_result->raw['precipAccumulation'])) {
                                    $snow_accum += $daily_result->raw['precipAccumulation'];

                                    if ($daily_result->raw['time'] > $snow_next)
                                        $snow_next = $daily_result->raw['time'];
                                }
                                break;

                            case "sleet":
                                // TODO determine if this counts as rain or snow
                                break;
                        }
                    }
                }
            }

            // convert next timestamps into relative offsets in hours, "hours until next event"
            if ($rain_next > 0) {
                $rain_next = $rain_next - ($rain_next % 3600);
                $arguments['$next_rain'] = ($rain_next - $now_hour) / 3600;
            } else
                $arguments['$next_rain'] = PHP_INT_MAX; // infinity

            if ($snow_next > 0) {
                $snow_next = $snow_next - ($snow_next % 3600);
                $arguments['$next_snow'] = ($snow_next - $now_hour) / 3600;
            } else
                $arguments['$next_snow'] = PHP_INT_MAX; // infinity

            $arguments['$next_rain_accum'] = $rain_accum;
            $arguments['$next_snow_accum'] = $snow_accum;
        }

        if ($results_last) {
            $temp_min = PHP_INT_MAX; // error
            $temp_max = PHP_INT_MIN; // error
            $rain_last = 0;
            $snow_last = 0;
            $rain_accum = 0;
            $snow_accum = 0;

            foreach ($results_last as $hourly_result) {
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

            // convert last timestamps into relative offsets in hours, "hours since last event"
            if ($rain_last > 0) {
                $rain_last = $rain_last - ($rain_last % 3600);
                $arguments['$last_rain'] = ($now_hour - $rain_last) / 3600;
            } else
                $arguments['$last_rain'] = PHP_INT_MAX; // infinity

            if ($snow_last > 0) {
                $snow_last = $snow_last - ($snow_last % 3600);
                $arguments['$last_snow'] = ($now_hour - $snow_last) / 3600;
            } else
                $arguments['$last_snow'] = PHP_INT_MAX; // infinity

            $arguments['$last_rain_accum'] = $rain_accum;
            $arguments['$last_snow_accum'] = $snow_accum;
        }

        return $arguments;
    }
}