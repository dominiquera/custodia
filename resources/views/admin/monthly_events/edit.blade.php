@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>Edit Monthly Event: {{$event->name}}</h1>
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
  <form action="/admin/monthly_events/update" method="POST" class="ui form">
      @csrf
      <input type="hidden" value="{{$event->id}}" name="id">

      <div class="form-group">
          <label for="month">Month</label>
          <select class="form-control" name="month" id="month">
              <option selected="selected" disabled="disabled">--Choose a Month--</option>
              <option @if ($event->month == "January") selected="selected" @endif>January</option>
              <option @if ($event->month == "February") selected="selected" @endif>February</option>
              <option @if ($event->month == "March") selected="selected" @endif>March</option>
              <option @if ($event->month == "April") selected="selected" @endif>April</option>
              <option @if ($event->month == "May") selected="selected" @endif>May</option>
              <option @if ($event->month == "June") selected="selected" @endif>June</option>
              <option @if ($event->month == "July") selected="selected" @endif>July</option>
              <option @if ($event->month == "August") selected="selected" @endif>August</option>
              <option @if ($event->month == "September") selected="selected" @endif>September</option>
              <option @if ($event->month == "October") selected="selected" @endif>October</option>
              <option @if ($event->month == "November") selected="selected" @endif>November</option>
              <option @if ($event->month == "December") selected="selected" @endif>December</option>
          </select>
      </div>

      <div class="form-group">
          <label for="title">Name</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Title" value="{{$event->title}}" required>
      </div>

      <h2>Maintenance Items</h2>
      <div class="maintenance-items-repeatable-container">
          @foreach ($event->maintenanceItems as $item)
              <div class="field-group" style="display: flex; margin-top: 10px;">
                  <select class="form-control" name="maintenance_items[]" id="maintenance_item_{{$item->id}}">
                      @foreach (Custodia\MaintenanceItem::all() as $availableItem)
                          <option value="{{$availableItem->id}}" @if ($availableItem->id == $item->id) selected="selected" @endif>{{ $availableItem->title }}</option>
                      @endforeach
                  </select>

                  <button  class="delete ui button negative" value="Remove">Remove</button>
              </div>
          @endforeach
      </div>
      <input type="button" class="add ui button" value="Add Maintenance Item" style="margin-top: 10px;"/>

      <br />

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection

@section('scripts')
    <script type="text/template" id="maintenance_items_repeater">
        <div class="field-group" style="display: flex; margin-top: 10px;">
            <select class="form-control" name="maintenance_items[]" id="maintenance_item_{?}">
                <option selected="selected" disabled="disabled">--Choose a Maintenance Item--</option>
                @foreach (Custodia\MaintenanceItem::all() as $item)
                    <option value="{{$item->id}}">{{ $item->title }}</option>
                @endforeach
            </select>

            <button  class="delete ui button negative" value="Remove">Remove</button>
        </div>
    </script>

    <script>
        $( document ).ready(function() {
            $("form .maintenance-items-repeatable-container").repeatable({
                template: "#maintenance_items_repeater"
            });
        });
    </script>
@endsection