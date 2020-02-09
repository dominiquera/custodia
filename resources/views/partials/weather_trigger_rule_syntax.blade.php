<pre>Rule Syntax:
------------

VARIABLES

$last_rain          Time since last rain (in hours)
$last_snow          Time since last snowfall (in hours)
$last_rain_accum    Rainfall in last 7 days (in mm)
$last_snow_accum    Snofall in last 7 days (in cm)
$last_temp_low      Lowest temperature in last 7 days
$last_temp_high     Highest temperature in last 7 days

$today_temp         Today's temperature (in C)
$today_snow         Today's snowfall (in cm)
$today_rain         Today's rainfall (in mm)

EXAMPLE RULES

$today_snow >= 5
$today_temp < -1
$today_rain > 0

$today_rain > 0 || $today_snow > 0
$last_rain >= 48 && $last_rain_accum > 10
</pre>