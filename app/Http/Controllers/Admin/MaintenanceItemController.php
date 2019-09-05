<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MaintenanceItem\CreateMaintenanceItemRequest;
use App\Http\Requests\MaintenanceItem\StoreMaintenanceItemRequest;
use App\MaintenanceItem;
use App\Section;
use DemeterChain\Main;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaintenanceItemController extends Controller
{
    public function maintenanceItems() {
        $items = MaintenanceItem::orderBy('id', 'desc')->paginate(10);
        return view('admin.maintenance_items.maintenance_items', ['items' => $items]);
    }

    public function newMaintenanceItem() {
        return view('admin.maintenance_items.new');
    }

    public function editMaintenanceItem($id) {
        $item = MaintenanceItem::findOrFail($id);
        return view('admin.maintenance_items.edit', ['item' => $item]);
    }


    public function createMaintenanceItem(CreateMaintenanceItemRequest $request)
    {
        $item = $this->saveMaintenanceItem($request);

        return redirect('/admin/maintenance_items');
    }

    private function saveMaintenanceItem($request){
        $item = new MaintenanceItem();
        $item->section_id = $request->section;
        $item->points = $request->points;
        $item->month = $request->month;
        $item->interval = $request->interval;
        $item->summary = $request->summary;

        $item->save();
        return $item;
    }

    public function updateMaintenanceItem(StoreMaintenanceItemRequest $request)
    {
        $item = MaintenanceItem::find($request->id);
        $item->section_id = $request->section;
        $item->points = $request->points;
        $item->month = $request->month;
        $item->interval = $request->interval;
        $item->summary = $request->summary;

        $item->save();

        return redirect('/admin/maintenance_items');
    }

    public function deleteMaintenanceItem($id)
    {
        $item = MaintenanceItem::findOrFail($id);
        $item->delete();
        return redirect('/admin/maintenance_items');
    }

    public function apiGetSectionMaintenanceItems(Section $section){
        return response()->json(['maintenance_items' => MaintenanceItem::where('section_id', '=', $section->id)->get()], 200);
    }
}
