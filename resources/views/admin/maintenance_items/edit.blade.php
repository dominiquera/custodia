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
  <form action="/admin/maintenance_items/update" method="POST" class="ui form">
      @csrf
      <input type="hidden" value="{{$item->id}}" name="id">
      <div class="form-group">
          <label for="role">Section</label>
          <select class="form-control" name="section" id="section">
              <option selected="selected" disabled="disabled">--Choose a Section--</option>
              @foreach (App\Section::all() as $section)
                  <option
                          @if ($section->id == $item->section->id)
                          selected="selected"
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
          <label for="month">Month</label>
          <select class="form-control" name="month" id="month">
              <option selected="selected" disabled="disabled">--Choose a Month--</option>
              <option @if ($item->month == "January") selected="selected" @endif>January</option>
              <option @if ($item->month == "February") selected="selected" @endif>February</option>
              <option @if ($item->month == "March") selected="selected" @endif>March</option>
              <option @if ($item->month == "April") selected="selected" @endif>April</option>
              <option @if ($item->month == "May") selected="selected" @endif>May</option>
              <option @if ($item->month == "June") selected="selected" @endif>June</option>
              <option @if ($item->month == "July") selected="selected" @endif>July</option>
              <option @if ($item->month == "August") selected="selected" @endif>August</option>
              <option @if ($item->month == "September") selected="selected" @endif>September</option>
              <option @if ($item->month == "October") selected="selected" @endif>October</option>
              <option @if ($item->month == "November") selected="selected" @endif>November</option>
              <option @if ($item->month == "December") selected="selected" @endif>December</option>
          </select>
      </div>
      <div class="form-group">
          <label for="interval">Interval</label>
          <input type="text" name="interval" class="form-control" id="interval" value="{{$item->interval}}">
      </div>
      <div class="form-group">
          <label for="summary">Summary</label>
          <textarea name="summary" class="form-control" id="summary" placeholder="Summary" required>{{$item->summary}}</textarea>
      </div>
      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
