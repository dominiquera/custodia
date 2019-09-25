@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
  <h1>Monthly Events</h1>
  <a href="/admin/monthly_events/new" class="ui button primary">New Monthly Event</a><br><br>
  @if (session('status'))
      <div class="alert alert-success" role="alert">
          {{ session('status') }}
      </div>
  @endif
  <table class="ui celled table">
      <thead>
      <tr>
          <th scope="col">ID</th>
          <th scope="col">Month</th>
          <th scope="col">Title</th>
          <th scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
      @foreach($events as $event)
          <tr>
              <td>{{$event->id}}</td>
              <td>{{$event->month}}</td>
              <td>{{$event->title}}</td>
              <td>
                  <a href="/admin/monthly_events/edit/{{$event->id}}">Edit</a> |
                  <a href="/admin/monthly_events/destroy/{{$event->id}}">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>
    <div class="motzi-admin-pagination">
        {{ $events->links() }}
    </div>
</div>
@endsection
