<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Controllers\Controller;
use Custodia\Http\Requests\WeatherTrigger\CreateWeatherTriggerRequest;
use Custodia\Http\Requests\WeatherTrigger\StoreWeatherTriggerRequest;
use Custodia\WeatherTriggerType;
use Illuminate\Http\Request;

class WeatherTriggerController extends Controller
{

    public function weatherTriggers() {
        $triggers = WeatherTriggerType::orderBy('id', 'desc')->paginate(10);
        return view('admin.weather_triggers.weather_triggers', ['triggers' => $triggers]);
    }

    public function newWeatherTrigger() {
        return view('admin.weather_triggers.new');
    }

    public function editWeatherTrigger($id) {
        $trigger = WeatherTriggerType::findOrFail($id);
        return view('admin.weather_triggers.edit', ['trigger' => $trigger]);
    }


    public function createWeatherTrigger(CreateWeatherTriggerRequest $request)
    {
        $type = $this->saveWeatherTrigger($request);

        return redirect('/admin/weather_triggers');
    }

    private function saveWeatherTrigger($request){
        $trigger = new WeatherTriggerType();
        $trigger->name = $request->name;
        $trigger->rule = $request->rule;

        $trigger->save();
        return $trigger;
    }

    public function updateWeatherTrigger(StoreWeatherTriggerRequest $request)
    {
        $trigger = WeatherTriggerType::find($request->id);
        $trigger->name = $request->name;
        $trigger->rule = $request->rule;

        $trigger->save();

        return redirect('/admin/weather_triggers');
    }

    public function deleteWeatherTrigger($id)
    {
        $trigger = WeatherTriggerType::findOrFail($id);
        $trigger->delete();
        return redirect('/admin/weather_triggers');
    }
}
