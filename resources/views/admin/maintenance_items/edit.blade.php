@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>Edit Maintenance Item: {{$item->name}}</h1>
  @if (session('status'))
      <div class="alert alert-success" role="alert">
          {{ session('status') }}
      </div>
  @endif
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form action="/admin/maintenance_items/update" method="POST" class="ui form" enctype="multipart/form-data">
      @csrf
      <input type="hidden" value="{{$item->id}}" name="id">
      <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title" value="{{$item->title}}">
      </div>
      <div class="form-group">
          <label for="role">Newsfeed Section</label>
          <select class="form-control" name="section" id="section">
              <option selected="selected" disabled="disabled">--Choose a Section--</option>
              @foreach (Custodia\Section::all() as $section)
                  <option
                          @if (isset($item->section))
                            @if ($section->id == $item->section->id)
                              selected="selected"
                            @endif
                          @endif
                          value="{{$section->id}}">{{ $section->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="points">Points</label>
          <input type="number" name="points" class="form-control" id="points" value="{{$item->points}}">
      </div>
      <div class="form-group">
          <label for="interval">Interval</label>
          <select class="form-control" name="interval" id="interval">
              <option selected="selected" disabled="disabled">--Choose an Interval--</option>
              @foreach (Custodia\Interval::all() as $interval)
                  <option
                          @if ($interval->id == $item->interval->id)
                          selected="selected"
                          @endif
                          value="{{$interval->id}}"
                          name="{{$interval->name}}">{{ $interval->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group" id="weather-trigger-form-group">
          <select class="form-control" name="trigger" id="trigger">
              <option selected="selected" disabled="disabled">--Choose a Weather Trigger--</option>
              @foreach (Custodia\WeatherTriggerType::all() as $trigger)
                  <option @if ($item->weather_trigger_type_id == $trigger->id) selected="selected" @endif value="{{$trigger->id}}">{{ $trigger->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="mobility_priority">Mobility Priority</label>
          <select class="form-control" name="mobility_priority" id="mobility_priority">
              <option value="0" @if($item->mobility_priority == false) selected="selected" @endif>No</option>
              <option value="1" @if($item->mobility_priority) selected="selected" @endif>Yes</option>
          </select>
      </div>
      <div class="form-group">
          <label for="home_types">Home Types</label>
          <div style="display: block;">
              @foreach (\Custodia\HomeType::all() as $homeType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="home_types[]"
                              value="{{$homeType->id}}"
                              @if ($item->homeTypes->contains($homeType)) checked="checked" @endif
                      >
                      <label>{{$homeType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="home_type">Outdoor Spaces Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\OutdoorSpaceType::all() as $spaceType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="outdoor_spaces[]"
                              value="{{$spaceType->id}}"
                              @if ($item->outdoorSpaces->contains($spaceType)) checked="checked" @endif
                      >
                      <label>{{$spaceType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="driveways">Driveways Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\DrivewayType::all() as $drivewayType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="driveways[]"
                              value="{{$drivewayType->id}}"
                              @if ($item->drivewayTypes->contains($drivewayType)) checked="checked" @endif
                      >
                      <label>{{$drivewayType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="features">Home Features Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\HomeFeature::all() as $featureType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="features[]"
                              value="{{$featureType->id}}"
                              @if ($item->homeFeatures->contains($featureType)) checked="checked" @endif
                      >
                      <label>{{$featureType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="mobility_issues">Mobility Issues Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\MobilityIssueType::all() as $mobilityIssueType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="mobility_issues[]"
                              value="{{$mobilityIssueType->id}}"
                              @if ($item->mobilityIssues->contains($mobilityIssueType)) checked="checked" @endif
                      >
                      <label>{{$mobilityIssueType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="summary">Summary</label>
          <textarea name="summary" class="form-control" id="summary" placeholder="Summary" required>{{$item->summary}}</textarea>
      </div>
      <div class="form-group">
          <label for="cautions">Cautions</label>
          <textarea name="cautions" class="form-control" id="cautions" placeholder="Cautions" required>{{$item->cautions}}</textarea>
      </div>

      <div class="months-repeatable-container">
          @foreach ($item->months as $month)
              <div class="field-group" style="display: flex; margin-top: 10px;">
                  <select class="form-control" name="months[]" id="month_{{$month->id}}">
                      <option value="January" @if ($month->month == "January") selected="selected" @endif >January</option>
                      <option value="February" @if ($month->month == "February") selected="selected" @endif >February</option>
                      <option value="March" @if ($month->month == "March") selected="selected" @endif >March</option>
                      <option value="April" @if ($month->month == "April") selected="selected" @endif >April</option>
                      <option value="May" @if ($month->month == "May") selected="selected" @endif >May</option>
                      <option value="June" @if ($month->month == "June") selected="selected" @endif >June</option>
                      <option value="July" @if ($month->month == "July") selected="selected" @endif >July</option>
                      <option value="August" @if ($month->month == "August") selected="selected" @endif >August</option>
                      <option value="September" @if ($month->month == "September") selected="selected" @endif >September</option>
                      <option value="October" @if ($month->month == "October") selected="selected" @endif >October</option>
                      <option value="November" @if ($month->month == "November") selected="selected" @endif>November</option>
                      <option value="December" @if ($month->month == "December") selected="selected" @endif>December</option>
                  </select>

                  <input type="text" name="descriptions[]" value="{{$month->description}}">

                  <button  class="delete ui button negative" value="Remove">Remove</button>
              </div>
          @endforeach
      </div>
      <input type="button" class="add ui button" value="Add Month" style="margin-top: 10px;"/>

      <div class="form-group">
          <label for="photo" >Photo</label>
          <input id="photo" type="file" class="form-control" name="photo" style="border:none;">
          @if (isset($item->featuredImage->path))
            <img style="max-width:300px;" src="{{asset($item->featuredImage->path)}}" />
          @endif
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection


@section('scripts')
    <script>
        $( document ).ready(function() {
            if ($('#interval').children("option:selected").attr('name') == "Weather Trigger"){
                $("#weather-trigger-form-group").show();
            } else {
                $("#weather-trigger-form-group").hide();
            }
        });

        $('#interval').on('change', function() {
            if ($(this).children("option:selected").attr('name') == "Weather Trigger"){
                $("#weather-trigger-form-group").show();
            } else {
                $("#weather-trigger-form-group").hide();
            }
        });
    </script>

    <script type="text/template" id="months-repeatable-container">
      <div class="field-group" style="display: flex; margin-top: 10px;">
          <select class="form-control" name="months[]" id="month_{?}">
              <option selected="selected" disabled="disabled">--Choose a Month--</option>
              <option value="January">January</option>
              <option value="February">February</option>
              <option value="March">March</option>
              <option value="April">April</option>
              <option value="May">May</option>
              <option value="June">June</option>
              <option value="July">July</option>
              <option value="August">August</option>
              <option value="September">September</option>
              <option value="October">October</option>
              <option value="November">November</option>
              <option value="December">December</option>
          </select>

          <input type="text" name="descriptions[]" id="description_{?}">

          <button  class="delete ui button negative" value="Remove">Remove</button>
      </div>
    </script>

    <script>
        $( document ).ready(function() {
            $("form .months-repeatable-container").repeatable({
                template: "#months-repeatable-container"
            });
        });
    </script>
@endsection
