@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>New User</h1>
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
  <form action="/admin/users/create" method="POST" class="ui form">
      @csrf
      <div class="form-group">
          <label for="name">Username</label>
          <input type="text" class="form-control" name="name" id="name" placeholder="Username" required>
      </div>
      <div class="form-group">
          <label for="email">E-Mail</label>
          <input type="text" name="email" class="form-control" id="email" placeholder="E-Mail" required>
      </div>
      <div class="form-group">
          <label for="address">Address</label>
          <input type="text" name="address" class="form-control" id="address" placeholder="Address" required>
      </div>
      <div class="form-group">
          <label for="city">City</label>
          <input type="text" name="city" class="form-control" id="city" placeholder="City">
      </div>
      <div class="form-group">
          <label for="zip">ZIP</label>
          <input type="text" name="zip" class="form-control" id="zip" placeholder="ZIP" required>
      </div>
      <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" class="form-control" id="password" required>
      </div>
      <div class="form-group">
          <label for="google_auth_id">Google Auth ID</label>
          <input type="text" class="form-control" name="google_auth_id" id="google_auth_id" placeholder="Google Auth ID">
      </div>
      <div class="form-group">
          <label for="firebase_registration_token">Firebase Registration Token</label>
          <input type="text" class="form-control" name="firebase_registration_token" id="firebase_registration_token"
                 placeholder="Firebase Registration Token">
      </div>
      <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="text" name="phone" class="form-control" id="phone" placeholder="Phone Number">
      </div>
      <div class="form-group">
          <label for="role">Role</label>
          <select class="form-control" name="role" id="role" required>
              <option selected="selected" disabled="disabled">--Choose a Role--</option>
              @foreach (Custodia\Role::all() as $role)
                  <option value="{{$role->id}}">{{ $role->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="home_type">Home Type</label>
          <select class="form-control" name="home_type" id="home_type" required>
              <option selected="selected" disabled="disabled">--Choose a Home Type--</option>
              @foreach (Custodia\HomeType::all() as $homeType)
                  <option value="{{$homeType->id}}">{{ $homeType->name }}</option>
              @endforeach
          </select>
      </div>
      <div class="form-group">
          <label for="home_type">Outdoor Spaces</label>
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
          <label for="driveways">Driveways</label>
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
          <label for="features">Home Features</label>
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
          <label for="mobility_issues">Mobility Issues</label>
          <div style="display: block;">
              @foreach (\Custodia\MobilityIssueType::all() as $mobilityIssue)
                  <div class="ui checkbox" style="display: block;">
                      <input type="checkbox" name="mobility_issues[]" value="{{$mobilityIssue->id}}">
                      <label>{{$mobilityIssue->name}}</label>
                  </div>
              @endforeach
          </div>
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
