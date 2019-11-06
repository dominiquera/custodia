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
          <th scope="col">Maintenance Item</th>
          <th scope="col">Points</th>
          <th scope="col">Newsfeed Section</th>
          <th scope="col">Interval</th>
          <th scope="col">Action</th>
      </tr>
      </thead>
      <tbody>
      @foreach($items as $item)
          <tr>
              <td>{{$item->id}}</td>
              <td>{{$item->title}}</td>
              <td>{{$item->points}}</td>
              <td>@if (isset($item->section)){{$item->section->name}}@endif</td>
              <td>{{$item->interval->name}}</td>
              <td>
                  <a href="/admin/maintenance_items/edit/{{$item->id}}">Edit</a> |
                  <a onclick="myFunction('{{url('/')}}/admin/maintenance_items/destroy/{{$item->id}}')" style="cursor: pointer;">Delete</a>
              </td>
          </tr>
      @endforeach
      </tbody>
  </table>
    <div class="motzi-admin-pagination">
        <div class="for-new-pagination" style="text-align: center;" >
            {{ $items->links() }}
        </div>
    </div>
</div>
<script>
    function myFunction(link) {
        let r = confirm("Sind Sie sicher, dass Sie löschen möchten?");

        if (r == true) {
            window.location.href = link;
        }
    }
</script>
@endsection
