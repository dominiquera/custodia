@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>Edit Event Type: {{$type->name}}</h1>
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
  <form action="/admin/monthly_events/update" method="POST" class="ui form">
      @csrf
      <input type="hidden" value="{{$type->id}}" name="id">
      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
