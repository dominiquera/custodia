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
          <label for="interval">Interval</label>
          <select class="form-control" name="interval" id="interval">
              <option selected="selected" disabled="disabled">--Choose an Interval--</option>
              @foreach (Custodia\Interval::all() as $interval)
                  <option
                          @if ($interval->id == $item->interval->id)
                          selected="selected"
                          @endif
                          value="{{$interval->id}}">{{ $interval->name }}</option>
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
          <label for="summary">Summary</label>
          <textarea name="summary" class="form-control" id="summary" placeholder="Summary" required>{{$item->summary}}</textarea>
      </div>
      <div class="form-group">
          <label for="cautions">Cautions</label>
          <textarea name="cautions" class="form-control" id="cautions" placeholder="Cautions" required>{{$item->cautions}}</textarea>
      </div>

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
