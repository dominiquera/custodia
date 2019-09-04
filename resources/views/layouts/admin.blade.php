<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/semantic.min.js') }}"></script>
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="vue" class="vue">
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
                <div class="right menu">
                    <a class="item" href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>

                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
              </div>
            </div>

          </nav>
        <main>
            @yield('content')
        </main>
    </div>
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    @yield('scripts');
</body>
</html>
