@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
  <h1>Event Types</h1>
  <a href="/admin/event_types/new" class="ui button primary">New Event Type</a><br><br>
  @if (session('status'))
      <div class="alert alert-success" role="alert">
          {{ session('status') }}
      </div>
  @endif
  <table class="ui celled table">
      <thead>
      <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
      @foreach($types as $type)
          <tr>
              <td>{{$type->id}}</td>
              <td>{{$type->name}}</td>
              <td>
                  <a href="/admin/event_types/edit/{{$type->id}}">Edit</a> |
                  <a href="/admin/event_types/destroy/{{$type->id}}">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>
    <div class="motzi-admin-pagination">
        {{ $types->links() }}
    </div>
</div>
@endsection
