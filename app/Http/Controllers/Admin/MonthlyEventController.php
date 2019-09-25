<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Controllers\Controller;
use Custodia\Http\Requests\MonthlyEvent\CreateMonthlyEventRequest;
use Custodia\Http\Requests\MonthlyEvent\StoreMonthlyEventRequest;
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


    public function createMonthlyEvent(CreateMonthlyEventRequest $request)
    {
        $item = $this->saveMonthlyEvent($request);

        return redirect('/admin/monthly_events');
    }

    private function saveMonthlyEvent($request){
        $item = new MonthlyEvent();
        $item->title = $request->title;
        $item->month = $request->month;
        $item->save();
        return $item;
    }

    public function updateMonthlyEvent(StoreMonthlyEventRequest $request)
    {
        $item = MonthlyEvent::find($request->id);
        $item->title = $request->title;
        $item->month = $request->month;
        $item->save();

        return redirect('/admin/monthly_events');
    }

    public function deleteMonthlyEvent($id)
    {
        $item = MonthlyEvent::findOrFail($id);
        $item->delete();
        return redirect('/admin/monthly_events');
    }
}
