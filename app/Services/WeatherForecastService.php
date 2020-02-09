<?php
namespace Custodia\Services;

use Custodia\WeatherForecast;
use Illuminate\Support\Facades\Log;

// TODO better support for timezones and future forecast information
class WeatherForecastService
{
    public function getDailyForecast(string $city, string $state, $date = false)
    {
        if (!$date)
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

    public function hasForecast(string $city, string $state, string $date, int $hour) : bool
    {
        return WeatherForecast::where([
            'for_date' => $date,
            'for_hour' => $hour,
            'city' => $city,
            'state' => $state
        ])->exists();
    }

    /**
     * @param string $city
     * @param string $state
     * @param string $date
     * @param int $hour
     * @param mixed $result
     */
    public function saveForecast(string $city, string $state, string $date, int $hour, $result): void
    {
        $weatherForecast = WeatherForecast::firstOrNew([
            'for_date' => $date,
            'for_hour' => $hour,
            'city' => $city,
            'state' => $state
        ]);

        $weatherForecast->for_date = $date;
        $weatherForecast->for_hour = $hour;
        $weatherForecast->city = $city;
        $weatherForecast->state = $state;
        $weatherForecast->raw = $result;

        $weatherForecast->save();
    }

    public function getPastResults(string $city, string $state, $date_from = false, $date_to = false)
    {
        if (!$date_from)
            $date_from = date('Y-m-d', strtotime('-7 days'));

        if (!$date_to)
            $date_to = date('Y-m-d');

        $weatherResults = WeatherForecast::where([
                'city' => $city,
                'state' => $state
            ])
            ->whereBetween('for_date', [$date_from, $date_to])
            ->orderBy('for_date', 'asc')
            ->orderBy('for_hour', 'asc')
            ->get();

        return $weatherResults;
    }
}