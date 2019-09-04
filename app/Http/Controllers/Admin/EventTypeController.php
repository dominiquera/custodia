<?php

namespace App\Http\Controllers\Admin;

use App\EventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventType\CreateEventTypeRequest;
use App\Http\Requests\EventType\StoreEventTypeRequest;
use Illuminate\Http\Request;

class EventTypeController extends Controller
{
    public function eventTypes() {
        $types = EventType::orderBy('id', 'desc')->paginate(10);
        return view('admin.event_types.event_types', ['types' => $types]);
    }

    public function newEventType() {
        return view('admin.event_types.new');
    }

    public function editEventType($id) {
        $type = EventType::findOrFail($id);
        return view('admin.event_types.edit', ['type' => $type]);
    }


    public function createEventType(CreateEventTypeRequest $request)
    {
        $type = $this->saveEventType($request);

        return redirect('/admin/event_types');
    }

    private function saveEventType($request){
        $item = new EventType();
        $item->name = $request->name;
        $item->short_description = $request->short_description;
        $item->long_description = $request->long_description;
        $item->icon = $request->icon;

        $item->save();
        return $item;
    }

    public function updateEventType(StoreEventTypeRequest $request)
    {
        $item = EventType::find($request->id);
        $item->name = $request->name;
        $item->short_description = $request->short_description;
        $item->long_description = $request->long_description;
        $item->icon = $request->icon;

        $item->save();

        return redirect('/admin/event_types');
    }

    public function deleteEventType($id)
    {
        $item = EventType::findOrFail($id);
        $item->delete();
        return redirect('/admin/event_types');
    }
}
