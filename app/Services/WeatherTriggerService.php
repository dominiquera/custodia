<?php

namespace Custodia\Services;

use Custodia\Tools\Evaluator;
use Custodia\User;
use Custodia\WeatherTriggerType;

class WeatherTriggerService
{
    /**
     * @var WeatherForecastService
     */
    private $weatherForecastService;

    /**
     * WeatherTriggerService constructor.
     *
     * @param WeatherForecastService $weatherForecastService
     */
    public function __construct(WeatherForecastService $weatherForecastService) {
        $this->weatherForecastService = $weatherForecastService;
    }

    /**
     * Check if a weather trigger has been activated
     *
     * @param WeatherTriggerType $weatherTriggerType
     * @param User               $user
     * @return bool|float|int|mixed
     */
    public function checkWeatherTrigger(WeatherTriggerType $weatherTriggerType, User $user)
    {
        // get user location
        $location = $this->getUserLocation($user);

        // test rule against weather for location
        return $this->testRuleAgainstWeatherForLocation($weatherTriggerType->rule, $location);
    }

    /**
     * Get the rule evaluation engine
     *
     * @param $expression
     * @return Evaluator
     */
    private function getRuleEvaluator($expression)
    {
        return new Evaluator($expression);
    }

    /**
     * Get the rule arguments
     *
     * @param $location
     * @return array
     */
    private function getRuleArguments($location): array
    {
        return $this->weatherForecastService->getTriggerData($location['city'], $location['state']) ?? [];
    }

    /**
     * Get the location data for a user
     *
     * @param User $user
     * @return array
     */
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

    /**
     * Test a rule for a given location
     *
     * @param $rule
     * @param $location
     * @return bool|float|int|mixed
     */
    private function testRuleAgainstWeatherForLocation($rule, $location)
    {
        $evaluator = $this->getRuleEvaluator($rule);
        $arguments = $this->getRuleArguments($location);

        if (!$arguments)
            return false;

        return $evaluator->evaluate($arguments);
    }
}