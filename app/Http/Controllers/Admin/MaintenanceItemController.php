<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Requests\MaintenanceItem\CreateMaintenanceItemRequest;
use Custodia\Http\Requests\MaintenanceItem\StoreMaintenanceItemRequest;
use Custodia\Image;
use Custodia\Interval;
use Custodia\MaintenanceItem;
use Custodia\Material;
use Custodia\Month;
use Custodia\MonthsDescription;
use Custodia\Section;
use Custodia\Tool;
use Custodia\User;
use DemeterChain\Main;
use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MaintenanceItemController extends Controller
{
    public function maintenanceItems()
    {
        $items = MaintenanceItem::orderBy('id', 'desc')->paginate(10);
        return view('admin.maintenance_items.maintenance_items', ['items' => $items]);
    }

    public function newMaintenanceItem()
    {
        return view('admin.maintenance_items.new');
    }

    public function editMaintenanceItem($id)
    {
        $item = MaintenanceItem::findOrFail($id);
        return view('admin.maintenance_items.edit', ['item' => $item]);
    }


    public function createMaintenanceItem(CreateMaintenanceItemRequest $request)
    {
        $item = $this->saveMaintenanceItem($request);

        return redirect('/admin/maintenance_items');
    }

    private function saveMaintenanceItem($request)
    {
        $item = new MaintenanceItem();
        $item->section_id = $request->section;
        $item->title = $request->title;
        $item->points = $request->points;
        $item->mobility_priority = $request->mobility_priority;
        $item->cautions = $request->cautions;
        $item->summary = $request->summary;
        $item->video = $request->video;
        $item->save();
        if (isset($request->tools)) {
            foreach ($request->tools as $tool) {
                $tools = new Tool();
                $tools->value = $tool;
                $tools->maintenance_items_id = $item->id;

                $tools->save();
            }
        }
        if (isset($request->materials)) {
            foreach ($request->materials as $material) {
                $materials = new Material();
                $materials->value = $material;
                $materials->maintenance_items_id = $item->id;

                $materials->save();
            }
        }
        if ($request->has('photo')) {
            $image = $request->file('photo');
            $this->updatedFeaturedImage($item, $image);
        }

//        $interval = Interval::find($request->interval);
//        if ($interval->name == "Weather Trigger") {
//            if ($request->has('trigger')) {
//                $item->weather_trigger_type_id = $request->trigger;
//            }
//        }

        if ($request->has('home_types')) {
            foreach ($request->home_types as $home_type) {
                $item->homeTypes()->attach($home_type);
            }
        }

        if ($request->has('outdoor_spaces')) {
            foreach ($request->outdoor_spaces as $outdoor_space) {
                $item->outdoorSpaces()->attach($outdoor_space);
            }
        }

        if ($request->has('driveways')) {
            foreach ($request->driveways as $driveway) {
                $item->drivewayTypes()->attach($driveway);
            }
        }

        if ($request->has('mobility_issues')) {
            foreach ($request->mobility_issues as $issue) {
                $item->mobilityIssues()->attach($issue);
            }
        }

        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                $item->homeFeatures()->attach($feature);
            }
        }


        if ($request->has('months')) {
            foreach ($request->months as $month) {
                if(isset($month['month'])){
                    $newMonth = new Month();
                    $newMonth->month = $month['month'];
                    $newMonth->maintenance_item_id = $item->id;
                    $newMonth->interval_id = (int)$month['interval'];
//                if (isset($request->photos[$i])) {
//                    $this->updatedFeaturedImageMonth($newMonth, $request->photos[$i]);
//                }
                    $newMonth->save();

                    if (isset($month['descriptions'])) {
                        foreach ($month['descriptions'] as $descriptions) {
                            if (!is_null($descriptions['text']) || (isset($descriptions['photos']) && !is_null($descriptions['photos']))) {
                                $newDesctiption = new MonthsDescription();
                                $newDesctiption->months_id = $newMonth->id;
                                if (!is_null($descriptions['text'])) {
                                    $newDesctiption->description = $descriptions['text'];
                                }
                                if (isset($descriptions['photos']) && !is_null($descriptions['photos'])) {
                                    $name = uniqid() . '_' . time();
                                    // Define folder path
                                    $folder = 'uploads/images/';
                                    // Make a file path where image will be stored [ folder path + file name + file extension]
                                    $filePath = "/storage/" . $folder . $name . '.' . $descriptions['photos']->getClientOriginalExtension();
                                    // Upload image
                                    $file = $descriptions['photos']->storeAs($folder, $name . '.' . $descriptions['photos']->getClientOriginalExtension(), 'public');
                                    $newDesctiption->img_name = $filePath;
                                }
                                $newDesctiption->save();
                            }
                        }
                    }
                }

            }
        }

        $item->save();

        return $item;
    }

    public function updateMaintenanceItem(StoreMaintenanceItemRequest $request)
    {
        Tool::where('maintenance_items_id', $request->id)->delete();
        Material::where('maintenance_items_id', $request->id)->delete();

        $item = MaintenanceItem::find($request->id);
        $item->section_id = $request->section;
        $item->title = $request->title;
        if (isset($request->video)) {
            $item->video = $request->video;
        }
        $item->points = $request->points;
        $item->mobility_priority = $request->mobility_priority;
        $item->cautions = $request->cautions;
        $item->summary = $request->summary;
        if ($request->has('photo')) {
            $image = $request->file('photo');
            $this->updatedFeaturedImage($item, $image);
        }

        $item->homeTypes()->detach();
        if ($request->has('home_types')) {
            $homeTypeScoreFactor = $request->has('home_type_score_factor') ? $request->home_type_score_factor : [];

            foreach ($request->home_types as $home_type) {
                $item->homeTypes()->attach($home_type, [
                    'score_factor' => isset($homeTypeScoreFactor[$home_type]) ? $homeTypeScoreFactor[$home_type] : 1
                ]);
            }
        }

        $item->outdoorSpaces()->detach();
        if ($request->has('outdoor_spaces')) {
            $outdoorSpaceScoreFactor = $request->has('outdoor_space_score_factor') ? $request->outdoor_space_score_factor : [];

            foreach ($request->outdoor_spaces as $outdoor_space) {
                $item->outdoorSpaces()->attach($outdoor_space, [
                    'score_factor' => isset($outdoorSpaceScoreFactor[$outdoor_space]) ? $outdoorSpaceScoreFactor[$outdoor_space] : 1
                ]);
            }
        }

        $item->drivewayTypes()->detach();
        if ($request->has('driveways')) {
            $drivewayScoreFactor = $request->has('driveway_score_factor') ? $request->driveway_score_factor : [];

            foreach ($request->driveways as $driveway) {
                $item->drivewayTypes()->attach($driveway, [
                    'score_factor' => isset($drivewayScoreFactor[$driveway]) ? $drivewayScoreFactor[$driveway] : 1
                ]);
            }
        }

        $item->mobilityIssues()->detach();
        if ($request->has('mobility_issues')) {
            $mobilityIssueScoreFactor = $request->has('mobility_issue_score_factor') ? $request->mobility_issue_score_factor : [];

            foreach ($request->mobility_issues as $issue) {
                $item->mobilityIssues()->attach($issue, [
                    'score_factor' => isset($mobilityIssueScoreFactor[$issue]) ? $mobilityIssueScoreFactor[$issue] : 1
                ]);
            }
        }

        $item->homeFeatures()->detach();
        if ($request->has('features')) {
            $homeFeatureScoreFactor = $request->has('feature_score_factor') ? $request->feature_score_factor : [];

            foreach ($request->features as $feature) {
                $item->homeFeatures()->attach($feature, [
                    'score_factor' => isset($drivewayScoreFactor[$feature]) ? $drivewayScoreFactor[$feature] : 1
                ]);
            }
        }
        MonthsDescription::whereIn('months_id', Month::where('maintenance_item_id', '=', $item->id)->get()->pluck('id'))->delete();
        Month::where('maintenance_item_id', '=', $item->id)->delete();

        $has_weather_trigger = false;
        
        if ($request->has('months')) {
            foreach ($request->months as $month) {
                if (isset($month['month'])) {
                    $newMonth = new Month();
                    $newMonth->month = $month['month'];
                    $newMonth->maintenance_item_id = $item->id;
                    $newMonth->interval_id = (int)$month['interval'];
                    $newMonth->save();
                    if (isset($month['descriptions'])) {
                        foreach ($month['descriptions'] as $descriptions) {
                            if (!is_null($descriptions['text']) || (isset($descriptions['photos']) && !is_null($descriptions['photos']))) {
                                $newDesctiption = new MonthsDescription();
                                $newDesctiption->months_id = $newMonth->id;
                                if (!is_null($descriptions['text'])) {
                                    $newDesctiption->description = $descriptions['text'];
                                }
                                if (isset($descriptions['photos']) && !is_null($descriptions['photos'])) {
                                    $name = uniqid() . '_' . time();
                                    // Define folder path
                                    $folder = 'uploads/images/';
                                    // Make a file path where image will be stored [ folder path + file name + file extension]
                                    $filePath = "/storage/" . $folder . $name . '.' . $descriptions['photos']->getClientOriginalExtension();
                                    // Upload image
                                    $file = $descriptions['photos']->storeAs($folder, $name . '.' . $descriptions['photos']->getClientOriginalExtension(), 'public');
                                    $newDesctiption->img_name = $filePath;
                                    if (isset($descriptions['old_photos'])) {
                                        File::delete($descriptions['old_photos']);
                                    }

                                } elseif (!isset($descriptions['photos']) && isset($descriptions['old_photos'])) {
                                    $newDesctiption->img_name = $descriptions['old_photos'];
                                }
                                $newDesctiption->save();
                            }
                        }
                    }
                    
                    if (!$has_weather_trigger) {
                        $interval = Interval::find((int)$month['interval']);

                        if ($interval->name == "Weather Trigger") {
                            $has_weather_trigger = true;
                        }
                    }
                }
            }
        }
        if ($has_weather_trigger && $request->has('trigger')) {
            $item->weather_trigger_type_id = $request->trigger;
        }
        $item->save();
        if (isset($request->tools)) {
            foreach ($request->tools as $tool) {
                $tools = new Tool();
                $tools->value = $tool;
                $tools->maintenance_items_id = $item->id;

                $tools->save();
            }
        }
        if (isset($request->materials)) {
            foreach ($request->materials as $material) {
                $materials = new Material();
                $materials->value = $material;
                $materials->maintenance_items_id = $item->id;

                $materials->save();
            }
        }

        return redirect('/admin/maintenance_items/edit/' . $item->id);
    }

    public function deleteMaintenanceItem($id)
    {
        Tool::where('maintenance_items_id', $id)->delete();
        Material::where('maintenance_items_id', $id)->delete();
        $item = MaintenanceItem::findOrFail($id);
        $item->delete();
        return redirect('/admin/maintenance_items');
    }

    public function updatedFeaturedImageMonth(Month $item, $image)
    {
        // Make a image name based on user name and current timestamp
        $name = str_slug($item->id) . '_' . time();
        // Define folder path
        $folder = 'uploads/images/';
        // Make a file path where image will be stored [ folder path + file name + file extension]
        $filePath = "/storage/" . $folder . $name . '.' . $image->getClientOriginalExtension();
        // Upload image
        $file = $image->storeAs($folder, $name . '.' . $image->getClientOriginalExtension(), 'public');

        // Create image object and link to maintenace item
        $image = new Image();
        $image->path = $filePath;
        $image->save();

        $item->featured_image_id = $image->id;
        $item->save();

    }

    public function updatedFeaturedImage(MaintenanceItem $item, $image)
    {
        // Make a image name based on user name and current timestamp
        $name = str_slug($item->id) . '_' . time();
        // Define folder path
        $folder = 'uploads/images/';
        // Make a file path where image will be stored [ folder path + file name + file extension]
        $filePath = "/storage/" . $folder . $name . '.' . $image->getClientOriginalExtension();
        // Upload image
        $file = $image->storeAs($folder, $name . '.' . $image->getClientOriginalExtension(), 'public');

        // Create image object and link to maintenace item
        $image = new Image();
        $image->path = $filePath;
        $image->save();

        $item->featured_image_id = $image->id;
        $item->save();
    }

    public function apiGetSectionMaintenanceItems(Section $section)
    {
        return response()->json(['maintenance_items' => MaintenanceItem::where('section_id', '=', $section->id)->get()], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiGetMaintenance($id)
    {
        $item = MaintenanceItem::where('id', $id)->with([
            'section', 'interval', 'featuredImage', 'weatherTriggerType', 'monthlyEvents',
            'months', 'homeTypes', 'outdoorSpaces', 'mobilityIssues', 'homeFeatures', 'drivewayTypes', 'tools', 'materials'])
            ->get();

        return response()->json(['data' => $item], 200);
    }
}
