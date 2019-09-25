@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>New Monthly Event</h1>
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
  <form action="/admin/monthly_events/create" method="POST" class="ui form">
      @csrf

      <div class="form-group">
          <label for="month">Month</label>
          <select class="form-control" name="month" id="month">
              <option selected="selected" disabled="disabled">--Choose a Month--</option>
              <option>January</option>
              <option>February</option>
              <option>March</option>
              <option>April</option>
              <option>May</option>
              <option>June</option>
              <option>July</option>
              <option>August</option>
              <option>September</option>
              <option>October</option>
              <option>November</option>
              <option>December</option>
          </select>
      </div>

      <div class="form-group">
          <label for="title">Name</label>
          <input type="text" name="title" class="form-control" id="title" placeholder="Title" required>
      </div>

      <h2>Maintenance Items</h2>
      <div class="maintenance-items-repeatable-container"></div>
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