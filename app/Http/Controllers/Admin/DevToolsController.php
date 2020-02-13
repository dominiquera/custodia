<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Controllers\Controller;
use Custodia\Services\UserService;
use Custodia\Services\WeatherForecastService;
use Illuminate\Http\Request;

class DevToolsController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var WeatherForecastService
     */
    private $weatherForecastService;

    public function __construct(UserService $userService, WeatherForecastService $weatherForecastService) {
        $this->userService = $userService;
        $this->weatherForecastService = $weatherForecastService;
    }

    public function index()
    {
        $locations = $this->userService->getUserLocations();

        return view('admin.devtools', [ 'locations' => $locations ]);
    }

    public function weather(string $state, string $city, Request $request)
    {
        $data = $request->validate([
            'date' => 'nullable|date'
        ]);

        $date = isset($data['date']) ? $data['date'] : null;

        return $this->weatherForecastService->getTriggerData($city, $state, $date);
    }
}
