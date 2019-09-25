@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>Edit Weather Trigger: {{$trigger->name}}</h1>
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
  <form action="/admin/weather_triggers/update" method="POST" class="ui form">
      @csrf
      <input type="hidden" value="{{$trigger->id}}" name="id">

      <div class="form-group">
          <label for="name">Name</label>
          <input type="text" name="name" class="form-control" id="name" placeholder="Name" value="{{$trigger->name}}" required>
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection