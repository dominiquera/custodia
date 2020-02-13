@extends('layouts.admin')

@section('content')
    <div class="container ui">
        <h1 class="ui header" style="margin-top:50px;">DevTools</h1>

        <div class="ui tabular menu">
            <div class="active item" data-tab="tab-weather-triggers">Weather Triggers</div>
            <!-- <div class="item" data-tab="tab-test">Test</div> -->
        </div>
        <div class="ui active tab" data-tab="tab-weather-triggers">
            <br/>
            <div class="ui stackable grid">
                <div class="six wide column">
                    <h3>Location</h3>
                    <div class="ui compact menu">
                        <div class="ui fluid search selection dropdown" id="weather-location">
                            <i class="dropdown icon"></i>
                            <div class="default text">Select Location</div>
                            <div class="menu">
                            @foreach($locations as $location)
                                <div class="item" data-city="{{$location->city}}" data-state="{{$location->state}}">{{$location->city}}, {{$location->state}}</div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <br/>

                    <h3>Date</h3>
                    <div class="ui calendar" id="weather-date">
                        <div class="ui input left icon">
                            <i class="calendar icon"></i>
                            <input type="text" placeholder="Date">
                        </div>
                    </div>
                    <br/>
                </div>
                <div class="ten wide column">
                    <div id="weather-results"><em>Select a location and date of forecast.</em></div>
                </div>
            </div>
        </div>
        <!--
        <div class="ui tab" data-tab="tab-test">

        </div>
        -->
    </div>
@endsection

@section('scripts')
    <script>
        window.weatherContext = {
            city: null,
            state: null,
            date: null
        };

        function updateWeatherResults() {
            if (window.weatherContext.city == null || window.weatherContext.state == null)
                return;

            var param = {
                date: window.weatherContext.date
            };

            var url = "/admin/devtools/weather/" + window.weatherContext.state + "/" + window.weatherContext.city
                + "?" + $.param(param);

            $.get(url,
                function(data) {
                    var $results = $('#weather-results');

                    if (!data) {
                        $results.html('No data');
                        return;
                    }

                    $results.html('');

                    for (var k in data) {
                        var val = data[k];
                        if (val === 9223372036854776000)
                            val = '∞';

                        $results.append('<div>' + k + ' = ' + val + '</div>');
                    }
                })
                .fail(function(data) {
                    var $results = $('#weather-results');
                    $results.html("An error occurred:<pre>>" + data.responseText + "</pre>");
                });
        }

        $(function() {
            $('.ui.tabular.menu .item').tab();

            $('#weather-location').dropdown({
                onChange: function (value, text, $choice) {
                    window.weatherContext.city = $choice.attr('data-city');
                    window.weatherContext.state = $choice.attr('data-state');
                    updateWeatherResults();
                }
            });

            var $weatherDate = $('#weather-date');
            
            $weatherDate.calendar({
                type: 'date',
                today: true,
                formatter: {
                    date: function (date, settings) {
                        if (!date) return '';
                        var day = date.getDate().toString().padStart(2, "0");
                        var month = (date.getMonth() + 1).toString().padStart(2, "0");
                        var year = date.getFullYear().toString().padStart(4, "0");
                        return year + '-' + month + '-' + day;
                    }
                },
                onChange: function (date, text, mode) {
                    window.weatherContext.date = text;
                    updateWeatherResults();
                }
            });

            // trigger onchange event to set initial state
            $weatherDate.calendar('set date', null);
        });
    </script>
@endsection
