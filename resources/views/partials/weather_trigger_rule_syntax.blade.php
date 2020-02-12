<pre>Rule Syntax:
------------

VARIABLES

$last_rain          Time since last rain (in hours)
$last_snow          Time since last snowfall (in hours)
$last_rain_accum    Rainfall in last 7 days (in mm)
$last_snow_accum    Snowfall in last 7 days (in cm)
$last_temp_low      Lowest temperature in last 7 days (in C)
$last_temp_high     Highest temperature in last 7 days (in C)

$today_temp         Today's temperature (in C)
$today_snow         Today's snowfall (in cm)
$today_rain         Today's rainfall (in mm)

$next_rain          Time until next rain (in hours)
$next_snow          Time until next snow (in hours)
$next_rain_accum    Rainfall in next 7 days (in mm)
$next_snow_accum    Snowfall in next 7 days (in cm)

EXAMPLE RULES

$today_snow >= 5
$today_temp < -1
$today_rain > 0

// rain or snow today
$today_rain > 0 || $today_snow > 0

// 48 hours after 10 MM or more of accumulated rainfall
$last_rain >= 48 && $last_rain_accum > 10

// forecasts 5 CM of snow or more 48 hours prior to the snowfall
$next_snow < 48 && $next_snow_accum >= 5
</pre>