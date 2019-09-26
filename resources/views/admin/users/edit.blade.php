@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>Edit User: {{$user->name}}</h1>
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
  <form action="/admin/users/update" method="POST" class="ui form">
      @csrf
      <input type="hidden" value="{{$user->id}}" name="id">
      <div class="form-group">
          <label for="name">Username</label>
          <input type="text" class="form-control" name="name" value="{{$user->name}}" id="name" placeholder="Username">
      </div>
      <div class="form-group">
          <label for="email">E-Mail</label>
          <input type="text" name="email" class="form-control" id="email" placeholder="E-Mail" value="{{$user->email}}">
      </div>
      <div class="form-group">
          <label for="role">Role</label>
          <select class="form-control" name="role" id="role">
              <option selected="selected" disabled="disabled">--Choose a Role--</option>
              @foreach (Custodia\Role::all() as $role)
                  <option value="{{$role->id}}"
                          @if ($role->id == $user->role_id)
                          selected="selected"
                          @endif
                  >{{ $role->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="home_type">Home Type</label>
          <select class="form-control" name="home_type" id="home_type">
              <option selected="selected" disabled="disabled">--Choose a Home Type--</option>
              @foreach (Custodia\HomeType::all() as $homeType)
                  <option value="{{$homeType->id}}"
                          @if ($homeType->id == $user->userProfile->home_type_id)
                          selected="selected"
                          @endif
                  >{{ $homeType->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="home_type">Outdoor Spaces</label>
          <div style="display: block;">
              @foreach (\Custodia\OutdoorSpaceType::all() as $spaceType)
                  <div class="ui checkbox" style="display: block;">
                      <input
                              type="checkbox"
                              name="outdoor_spaces[]"
                              value="{{$spaceType->id}}"
                              @if ($user->userProfile->outdoorSpaces->contains($spaceType)) checked="checked" @endif
                      >
                      <label>{{$spaceType->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>
      <div class="form-group">
          <label for="score">Score</label>
          <input type="number" name="score" class="form-control" id="score" placeholder="Score" value="{{$user->userProfile->score}}">
      </div>
      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
