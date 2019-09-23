@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h2>New User</h2>
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
  <form id="app"
        action="/admin/users/create"
        method="POST"
        class="ui form"
        enctype="multipart/form-data"
        data-user="4">
      @csrf
      <h3>User</h3>
      <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
      </div>
      <div class="form-group">
          <label for="email">E-Mail</label>
          <input type="text" name="email" class="form-control" id="email" placeholder="E-Mail" required>
      </div>
      <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
      </div>
      <div class="form-group">
          <label for="price">Role</label>
          <input type="text" name="role" class="form-control" id="role" required>
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection

@section('scripts')

@endsection
