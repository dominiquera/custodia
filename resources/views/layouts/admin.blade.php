<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dist/vue2-autocomplete.css') }}" rel="stylesheet">
    <link href="https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/76959c6f7d33a527b49be76789e984a0a407350b/dist/calendar.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="app">
        <head>
          <nav>
            <div class="ui container" style="padding-top:50px">
                <div class="ui menu">
                @if(Auth::check() && Auth::user()->role == "admin")
                  <a href="{{ route('admin') }}" class="item">
                    Dashboard
                  </a>
                @endif
                <a href="{{ route('manage-users') }}" class="item">
                  Users
                </a>

                <a class="item" href="{{ url('/logout') }}"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
              </div>
            </div>

          </nav>
        </head>
        <main>
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/semantic.min.js') }}"></script>
    <script src="{{ asset('js/calendar.js') }}"></script>
    @yield('scripts');

</body>
</html>
