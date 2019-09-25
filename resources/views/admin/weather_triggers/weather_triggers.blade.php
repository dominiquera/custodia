@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
  <h1>Weather Triggers</h1>
  <a href="/admin/weather_triggers/new" class="ui button primary">New Weather Trigger</a><br><br>
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
      @foreach($triggers as $trigger)
          <tr>
              <td>{{$trigger->id}}</td>
              <td>{{$trigger->name}}</td>
              <td>
                  <a href="/admin/weather_triggers/edit/{{$trigger->id}}">Edit</a> |
                  <a href="/admin/weather_triggers/destroy/{{$trigger->id}}">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>
    <div class="motzi-admin-pagination">
        {{ $triggers->links() }}
    </div>
</div>
@endsection
