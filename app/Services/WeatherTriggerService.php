<?php

namespace Custodia\Services;

use Custodia\Tools\Evaluator;
use Custodia\User;
use Custodia\WeatherForecast;
use Custodia\WeatherTriggerType;
use Illuminate\Support\Facades\Log;

class WeatherTriggerService
{
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
        $forecast = $this->getWeatherForecast($location['city'], $location['state']);

        if (empty($forecast)) {
            Log::error("unable to test weather trigger rule; no weather forecast", $location);
            return false;
        }

        $evaluator = $this->getRuleEvaluator($rule);
        $arguments = $this->getRuleArgumentsFromForecast($forecast);

        return $evaluator->evaluate($arguments);
    }

    private function getWeatherForecast($city, $state)
    {
        // TODO better support for timezones and future/past forecast information
        $date = date('Y-m-d');

        $weatherForecast = WeatherForecast::where('for_date', $date)
            ->where('city', $city)
            ->where('state', $state)
            ->first();

        if (empty($weatherForecast) || empty($weatherForecast->raw)) {
            Log::error("weather forecast not found in database",
                ['date' => $date, 'city' => $city, 'state' => $state]);
            return false;
        }

        foreach ($weatherForecast->raw['daily']['data'] as $daily_data) {
            if (date('Y-m-d', $daily_data['time']) == $date)
                return $daily_data;
        }

        Log::error("missing weather forecast", ['date' => $date, 'city' => $city, 'state' => $state]);
        return false;
    }

    private function getRuleEvaluator($expression)
    {
        return new Evaluator($expression);
    }

    private function getRuleArgumentsFromForecast( $forecast): array
    {
        $arguments = [
            '$today_rain' => 0,
            '$today_snow' => 0,
            '$today_temp' => $forecast['temperatureHigh'],
        ];

        if ($forecast['precipIntensity'] > 0) {
            switch ($forecast['precipType']) {
                case "rain":
                    $arguments['$today_rain'] = $forecast['precipIntensity'] * 24;
                    break;

                case "snow":
                    $arguments['$today_snow'] = $forecast['precipIntensity'] * 24;
                    break;

                case "sleet":
                    // TODO determine if this counts as rain or snow
                    break;
            }
        }

        return $arguments;
    }
}
