@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <div>
        <div style="float:left;">
            <h2>Users</h2>
        </div>
        <div style="float:right;">
            <a href="/admin/users/new" class="ui button primary">New User</a><br><br>
        </div>
    </div>
  @if (session('status'))
      <div class="alert alert-success" role="alert">
          {{ session('status') }}
      </div>
  @endif
  <table class="ui celled table">
      <thead>
      <tr>
          <th scope="col">ID</th>
          <th scope="col">Username</th>
          <th scope="col">Email</th>
          <th scope="col">Role</th>
          <th scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
      @foreach($users as $user)
          <tr>
              <td>{{$user->id}}</td>
              <td>{{$user->name}}</td>
              <td>{{$user->email}}</td>
              <td>{{$user->role}}</td>
              <td>
                  <a href="/admin/users/edit/{{$user->id}}">Edit</a> |
                  <a href="/admin/users/destroy/{{$user->id}}">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>

    <div class="admin-pagination">
        {{ $users->links() }}
    </div>
</div>
@endsection
