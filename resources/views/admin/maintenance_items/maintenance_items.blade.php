@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
  <h1>Maintenance Items</h1>
  <a href="/admin/maintenance_items/new" class="ui button primary">New Maintenance Item</a><br><br>
  @if (session('status'))
      <div class="alert alert-success" role="alert">
          {{ session('status') }}
      </div>
  @endif
  <table class="ui celled table">
      <thead>
      <tr>
          <th scope="col">ID</th>
          <th scope="col">Section</th>
          <th scope="col">Points</th>
          <th scope="col">Month</th>
          <th scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
      @foreach($items as $item)
          <tr>
              <td>{{$item->id}}</td>
              <td>{{$item->section->name}}</td>
              <td>{{$item->points}}</td>
              <td>{{$item->month}}</td>
              <td>
                  <a href="/admin/maintenance_items/edit/{{$item->id}}">Edit</a> |
                  <a href="/admin/maintenance_items/destroy/{{$item->id}}">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>
    <div class="motzi-admin-pagination">
        {{ $items->links() }}
    </div>
</div>
@endsection
