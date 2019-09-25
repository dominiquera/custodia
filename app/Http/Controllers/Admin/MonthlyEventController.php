<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Controllers\Controller;
use Custodia\Http\Requests\MonthlyEvent\CreateWeatherTriggerRequest;
use Custodia\Http\Requests\MonthlyEvent\StoreWeatherTriggerRequest;
use Custodia\MaintenanceItem;
use Custodia\MonthlyEvent;
use Illuminate\Http\Request;

class MonthlyEventController extends Controller
{
    public function monthlyEvents() {
        $events = MonthlyEvent::orderBy('id', 'desc')->paginate(10);
        return view('admin.monthly_events.monthly_events', ['events' => $events]);
    }

    public function newMonthlyEvent() {
        return view('admin.monthly_events.new');
    }

    public function editMonthlyEvent($id) {
        $event = MonthlyEvent::findOrFail($id);
        return view('admin.monthly_events.edit', ['event' => $event]);
    }


    public function createMonthlyEvent(CreateWeatherTriggerRequest $request)
    {
        $item = $this->saveMonthlyEvent($request);

        return redirect('/admin/monthly_events');
    }

    private function saveMonthlyEvent($request){
        $event = new MonthlyEvent();
        $event->title = $request->title;
        $event->month = $request->month;
        $event->save();

        if ($request->has('maintenance_items')){
            foreach($request->maintenance_items as $item_id){
                $item = MaintenanceItem::find($item_id);
                if ($item){
                    $event->maintenanceItems()->attach($item->id);
                }
            }
        }

        $event->save();
        return $event;
    }

    public function updateMonthlyEvent(StoreWeatherTriggerRequest $request)
    {
        $event = MonthlyEvent::find($request->id);
        $event->title = $request->title;
        $event->month = $request->month;

        $event->maintenanceItems()->detach();
        if ($request->has('maintenance_items')){
            foreach($request->maintenance_items as $item_id){
                $item = MaintenanceItem::find($item_id);
                if ($item){
                    $event->maintenanceItems()->attach($item->id);
                }
            }
        }

        $event->save();
        return redirect('/admin/monthly_events');
    }

    public function deleteMonthlyEvent($id)
    {
        $item = MonthlyEvent::findOrFail($id);
        $item->delete();
        return redirect('/admin/monthly_events');
    }
}
