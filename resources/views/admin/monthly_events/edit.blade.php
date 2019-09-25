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
      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
