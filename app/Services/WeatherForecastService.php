<?php
namespace Custodia\Services;

use Custodia\WeatherForecast;
use Illuminate\Support\Facades\Log;

// TODO better support for timezones and future forecast information
class WeatherForecastService
{
    /**
     * Get the data required for weather trigger arguments
     *
     * @param string $city
     * @param string $state
     * @param null   $date
     * @return array|null
     */
    public function getTriggerData(string $city, string $state, $date = null): ?array
    {
        if ($date == null)
            $date = date('Y-m-d');

        $now = strtotime($date);
        $from_date = date('Y-m-d', strtotime('-7 days', $now));
        $results_last = $this->getPastResults($city, $state, $from_date, $date);
        $results_next = $this->getFutureResults($city, $state, $date);

        $results_today = $this->getTodayResults($city, $state, $date);

        if (empty($results_today) || empty($results_today['currently'])) {
            Log::error("unable to test weather trigger rule; no daily weather forecast",
                       [ 'city' => $city, 'state' => $state ]);
            return null;
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
            '$today_temp' => PHP_INT_MIN, // infinity
        ];

        $now_hour = $now - ($now % 3600); // round to nearest hour to keep offsets whole

        // SECTION: "TODAY"
        // - includes real-time data up to the hour
        // - includes forecasted data for remainder of day
        $today_hours = (int)date('H', $now_hour);

        if (empty($results_today['history'])) {
            $today_hours = 24;
        } else {
            $results = count($results_today['history']);

            if (($missing_hours = (24 - $today_hours - $results)) > 0)
                $today_hours += $missing_hours;

            foreach ($results_today['history'] as $hourly_result) {
                if ($hourly_result->raw['currently']['temperature'] > $arguments['$today_temp'])
                    $arguments['$today_temp'] = $hourly_result->raw['currently']['temperature'];

                if (isset($hourly_result->raw['currently']['precipProbability']) &&
                    isset($hourly_result->raw['currently']['precipType'])) {
                    if ($hourly_result->raw['currently']['precipProbability'] > 0) {
                        switch ($hourly_result->raw['currently']['precipType']) {
                            case "rain":
                                if (isset($hourly_result->raw['currently']['precipAccumulation']))
                                    $arguments['$today_rain'] += $hourly_result->raw['currently']['precipAccumulation'];
                                break;

                            case "snow":
                                if (isset($hourly_result->raw['currently']['precipAccumulation']))
                                    $arguments['$today_snow'] += $hourly_result->raw['currently']['precipAccumulation'];
                                break;

                            case "sleet":
                                // TODO determine if this counts as rain or snow
                                break;
                        }
                    }
                }
            }
        }

        if ($results_today['currently']['temperature'] > $arguments['$today_temp'])
            $arguments['$today_temp'] = $results_today['currently']['temperature'];

        if ($results_today['currently']['precipIntensity'] > 0) {
            switch ($results_today['currently']['precipType']) {
                case "rain":
                    $arguments['$today_rain'] += $results_today['currently']['precipIntensity'];
                    $today_hours--;
                    break;

                case "snow":
                    $arguments['$today_snow'] += $results_today['currently']['precipIntensity'] * (24 - $today_hours);
                    $today_hours--;
                    break;

                case "sleet":
                    // TODO determine if this counts as rain or snow
                    break;
            }

            if ($today_hours < 0)
                $today_hours = 0;
        }

        if (!empty($results_today['daily']) && isset($results_today['daily']['data'])) {
            foreach ($results_today['daily']['data'] as $daily_result) {
                if ($daily_result['time'] == $now) {
                    if ($daily_result['temperatureHigh'] > $arguments['$today_temp'])
                        $arguments['$today_temp'] = $daily_result['temperatureHigh'];

                    if ($daily_result['precipIntensity'] > 0) {
                        switch ($daily_result['precipType']) {
                            case "rain":
                                $arguments['$today_rain'] += $daily_result['precipIntensity'] * (24 - $today_hours);
                                break;

                            case "snow":
                                $arguments['$today_snow'] += $daily_result['precipIntensity'] * (24 - $today_hours);
                                break;

                            case "sleet":
                                // TODO determine if this counts as rain or snow
                                break;
                        }
                    }

                    break;
                }
            }
        }

        // SECTION: "NEXT"
        // - includes forecasted data for the next 7 days
        if ($results_next && isset($results_next->raw['daily'])) {
            $rain_next = PHP_INT_MAX; // infinity
            $snow_next = PHP_INT_MAX; // infinity
            $rain_accum = 0;
            $snow_accum = 0;

            foreach ($results_next->raw['daily']['data'] as $daily_result) {
                if (isset($daily_result['precipProbability']) &&
                    isset($daily_result['precipType'])) {

                    if ($daily_result['precipProbability'] > 0) {
                        switch ($daily_result['precipType']) {
                            case "rain":
                                if (isset($daily_result['precipAccumulation'])) {
                                    $rain_accum += $daily_result['precipAccumulation'];

                                    if ($daily_result['time'] < $rain_next)
                                        $rain_next = $daily_result['time'];
                                }
                                break;

                            case "snow":
                                if (isset($daily_result['precipAccumulation'])) {
                                    $snow_accum += $daily_result['precipAccumulation'];

                                    if ($daily_result['time'] < $snow_next)
                                        $snow_next = $daily_result['time'];
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
            if ($rain_next > 0 && $rain_next != PHP_INT_MAX) {
                $rain_next = $rain_next - ($rain_next % 3600);

                if ($rain_next < $now_hour)
                    $rain_next = $now_hour;

                $arguments['$next_rain'] = ($rain_next - $now_hour) / 3600;
            }else if ($rain_next == 0)
                $arguments['$next_rain'] = 0;
            else
                $arguments['$next_rain'] = PHP_INT_MAX; // infinity

            if ($snow_next > 0 && $snow_next != PHP_INT_MAX) {
                $snow_next = $snow_next - ($snow_next % 3600);

                if ($snow_next < $now_hour)
                    $snow_next = $now_hour;

                $arguments['$next_snow'] = ($snow_next - $now_hour) / 3600;
            }else if ($snow_next <= 0)
                $arguments['$next_snow'] = 0;
            else
                $arguments['$next_snow'] = PHP_INT_MAX; // infinity

            $arguments['$next_rain_accum'] = $rain_accum;
            $arguments['$next_snow_accum'] = $snow_accum;
        }

        // SECTION: "LAST"
        // - includes historical data for the past 7 days
        if ($results_last) {
            $temp_min = PHP_INT_MAX; // error
            $temp_max = PHP_INT_MIN; // error
            $rain_last = PHP_INT_MIN; // infinity
            $snow_last = PHP_INT_MIN; // infinity
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
            if ($rain_last > 0 && $rain_last != PHP_INT_MIN) {
                $rain_last = $rain_last - ($rain_last % 3600);

                if ($rain_last > $now_hour)
                    $rain_last = $now_hour;

                $arguments['$last_rain'] = ($now_hour - $rain_last) / 3600;
            } else if ($rain_last == 0)
                $arguments['$last_rain'] = 0;
            else
                $arguments['$last_rain'] = PHP_INT_MAX; // infinity

            if ($snow_last > 0 && $snow_last != PHP_INT_MIN) {
                $snow_last = $snow_last - ($snow_last % 3600);

                if ($snow_last > $now_hour)
                    $snow_last = $now_hour;

                $arguments['$last_snow'] = ($now_hour - $snow_last) / 3600;
            } else if ($snow_last == 0)
                $arguments['$last_snow'] = 0;
            else
                $arguments['$last_snow'] = PHP_INT_MAX; // infinity

            $arguments['$last_rain_accum'] = $rain_accum;
            $arguments['$last_snow_accum'] = $snow_accum;
        }

        return $arguments;
    }

    /**
     * Get historical results for a given date range
     *
     * @param string $city
     * @param string $state
     * @param bool   $date_from
     * @param bool   $date_to
     * @return mixed
     */
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

    /**
     * Get the forecasted results on a given date
     *
     * @param string $city
     * @param string $state
     * @param bool   $date
     * @return mixed
     */
    public function getFutureResults(string $city, string $state, $date = false)
    {
        if (!$date)
            $date = date('Y-m-d');

        return $this->getLatestResult($city, $state, $date);
    }

    /**
     * Get the current results for a given date
     *
     * @param string $city
     * @param string $state
     * @param bool   $date
     * @return array|bool
     */
    public function getTodayResults(string $city, string $state, $date = false)
    {
        if (!$date)
            $date = date('Y-m-d');

        $weatherForecast = $this->getLatestResult($city, $state, $date);

        if (empty($weatherForecast) || empty($weatherForecast->raw)) {
            Log::error("weather forecast not found in database",
                       ['date' => $date, 'city' => $city, 'state' => $state]);
            return false;
        }

        return [
            'currently' => $weatherForecast->raw['currently'],
            'daily' => isset($weatherForecast->raw['daily']) ? $weatherForecast->raw['daily'] : null,
            'history' =>$this->getPastResults($city, $state, $date, $date)
        ];
    }

    /**
     * Get the latest forecast result for a given date
     *
     * @param string $city
     * @param string $state
     * @param $date
     * @return mixed
     */
    private function getLatestResult(string $city, string $state, $date)
    {
        $weatherForecast = WeatherForecast::where('for_date', $date)
                                          ->where('city', $city)
                                          ->where('state', $state)
                                          ->orderBy('for_date', 'desc')
                                          ->orderBy('for_hour', 'desc')
                                          ->first();
        return $weatherForecast;
    }

    /**
     * Check if forecast data is present
     *
     * @param string $city
     * @param string $state
     * @param string $date
     * @param int    $hour
     * @return bool
     */
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
     * Save a weather forecast result
     *
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
}

