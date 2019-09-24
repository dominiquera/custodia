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
  <form action="/admin/maintenance_items/create" method="POST" class="ui form">
      @csrf
      <div class="form-group">
          <label for="section">Section</label>
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
          <label for="interval">Interval</label>
          <select class="form-control" name="interval" id="interval">
              <option selected="selected" disabled="disabled">--Choose an Interval--</option>
              @foreach (Custodia\Interval::all() as $interval)
                  <option value="{{$interval->id}}">{{ $interval->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="summary">Summary</label>
          <textarea name="summary" class="form-control" id="summary" placeholder="Summary" required></textarea>
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
