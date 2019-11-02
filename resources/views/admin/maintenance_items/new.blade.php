@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>New Maintenance Item</h1>
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
  <form action="/admin/maintenance_items/create" method="POST" class="ui form" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
          <label for="title">Title</label>
          <input type="text" name="title" class="form-control" id="title">
      </div>
      <div class="form-group">
          <label for="section">Newsfeed Section</label>
          <select class="form-control" name="section" id="section">
              <option selected="selected" disabled="disabled">--Choose a Section--</option>
              @foreach (Custodia\Section::all() as $section)
                  <option value="{{$section->id}}">{{ $section->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="points">Points</label>
          <input type="number" name="points" class="form-control" id="points">
      </div>
      <div class="form-group">
          <label for="interval">Interval</label>
          <select class="form-control" name="interval" id="interval">
              <option selected="selected" disabled="disabled">--Choose an Interval--</option>
              @foreach (Custodia\Interval::all() as $interval)
                  <option value="{{$interval->id}}" name="{{$interval->name}}">{{ $interval->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group" id="weather-trigger-form-group">
          <select class="form-control" name="trigger" id="trigger">
              <option selected="selected" disabled="disabled">--Choose a Weather Trigger--</option>
              @foreach (Custodia\WeatherTriggerType::all() as $trigger)
                  <option value="{{$trigger->id}}">{{ $trigger->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="mobility_priority">Mobility Priority</label>
          <select class="form-control" name="mobility_priority" id="mobility_priority">
              <option value="0" selected="selected">No</option>
              <option value="1">Yes</option>
          </select>
      </div>
      <div class="form-group">
          <label for="home_type">Home Types Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\HomeType::all() as $homeType)
                  <div class="ui checkbox" style="display: block;">
                      <input type="checkbox" name="home_types[]" value="{{$homeType->id}}">
                      <label>{{$homeType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="outdoor_spaces">Outdoor Spaces Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\OutdoorSpaceType::all() as $spaceType)
                  <div class="ui checkbox" style="display: block;">
                      <input type="checkbox" name="outdoor_spaces[]" value="{{$spaceType->id}}">
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
                      <input type="checkbox" name="driveways[]" value="{{$drivewayType->id}}">
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
                      <input type="checkbox" name="features[]" value="{{$featureType->id}}">
                      <label>{{$featureType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="mobility_issues">Mobility Issues Applicable</label>
          <div style="display: block;">
              @foreach (\Custodia\MobilityIssueType::all() as $mobilityIssue)
                  <div class="ui checkbox" style="display: block;">
                      <input type="checkbox" name="mobility_issues[]" value="{{$mobilityIssue->id}}">
                      <label>{{$mobilityIssue->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="summary">Summary</label>
          <textarea name="summary" class="form-control" id="summary" placeholder="Summary" required></textarea>
      </div>

      <div class="months-repeatable-container"></div>
      <input type="button" class="add ui button" value="Add Month" style="margin-top: 10px;"/>

      <div class="form-group">
          <label for="cautions">Cautions</label>
          <textarea name="cautions" class="form-control" id="cautions" placeholder="Cautions" required></textarea>
      </div>
      <div class="form-group">
          <label for="photo" >Featured Image</label>
          <input id="photo" type="file" class="form-control" name="photo" style="border:none;">
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection

@section('scripts')
    <script>
        $( document ).ready(function() {
            $("#weather-trigger-form-group").hide();
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
                <option value="November">November</option>
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
