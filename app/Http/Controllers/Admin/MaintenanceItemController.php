<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Requests\MaintenanceItem\CreateMaintenanceItemRequest;
use Custodia\Http\Requests\MaintenanceItem\StoreMaintenanceItemRequest;
use Custodia\Image;
use Custodia\Interval;
use Custodia\MaintenanceItem;
use Custodia\Section;
use DemeterChain\Main;
use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;

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
        $item->interval_id = $request->interval;
        $item->title = $request->title;
        $item->points = $request->points;
        $item->mobility_priority = $request->mobility_priority;
        $item->cautions = $request->cautions;
        $item->summary = $request->summary;
        if ($request->has('photo')) {
            $image = $request->file('photo');
            $this->updatedFeaturedImage($item, $image);
        }

        $interval = Interval::find($request->interval);
        if ($interval->name == "Weather Trigger"){
            if ($request->has('trigger')){
                $item->weather_trigger_type_id = $request->trigger;
            }
        }
        $item->save();
        return $item;
    }

    public function updateMaintenanceItem(StoreMaintenanceItemRequest $request)
    {
        $item = MaintenanceItem::find($request->id);
        $item->section_id = $request->section;
        $item->interval_id = $request->interval;
        $item->title = $request->title;
        $item->points = $request->points;
        $item->mobility_priority = $request->mobility_priority;
        $item->cautions = $request->cautions;
        $item->summary = $request->summary;
        if ($request->has('photo')) {
            $image = $request->file('photo');
            $this->updatedFeaturedImage($item, $image);
        }

        $interval = Interval::find($request->interval);
        if ($interval->name == "Weather Trigger"){
            if ($request->has('trigger')){
                $item->weather_trigger_type_id = $request->trigger;
            }
        }
        $item->save();

        return redirect('/admin/maintenance_items');
    }

    public function deleteMaintenanceItem($id)
    {
        $item = MaintenanceItem::findOrFail($id);
        $item->delete();
        return redirect('/admin/maintenance_items');
    }

    public function updatedFeaturedImage(MaintenanceItem $item, $image){
        // Make a image name based on user name and current timestamp
        $name = str_slug($item->id).'_'.time();
        // Define folder path
        $folder = '/uploads/images/';
        // Make a file path where image will be stored [ folder path + file name + file extension]
        $filePath = "/storage/" . $folder . $name. '.' . $image->getClientOriginalExtension();
        // Upload image
        $file = $image->storeAs($folder, $name.'.'.$image->getClientOriginalExtension(), 'public');

        // Create image object and link to maintenace item
        $image = new Image();
        $image->path = $filePath;
        $image->save();

        $item->featured_image_id = $image->id;
        $item->save();
    }

    public function apiGetSectionMaintenanceItems(Section $section){
        return response()->json(['maintenance_items' => MaintenanceItem::where('section_id', '=', $section->id)->get()], 200);
    }

}
