@extends('layouts.admin')

@section('content')
<div class="ui container" style="padding-top:50px;">
    <h1>New Maintenance Item</h1>
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
  <form action="/admin/event_types/create" method="POST" class="ui form">
      @csrf

      <div class="form-group">
          <label for="name">Name</label>
          <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
      </div>

      <div class="form-group">
          <label for="short_description">Short Description</label>
          <input type="text" name="short_description" class="form-control" id="short_description" placeholder="Short Description" required>
      </div>

      <div class="form-group">
          <label for="long_description">Long Description</label>
          <textarea name="long_description" class="form-control" id="long_description" placeholder="Long Description" required></textarea>
      </div>

      <div class="form-group">
          <label for="icon">Icon</label>
          <input type="text" name="icon" class="Icon-control" id="icon" placeholder="Icon" required>
      </div>

      <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>
  </form>
</div>
@endsection
