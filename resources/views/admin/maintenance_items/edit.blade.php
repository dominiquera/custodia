@extends('layouts.admin')

@section('content')
    <div class="ui container" style="padding-top:50px;">
        <h1>Edit Maintenance Item: {{$item->name}}</h1>
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
        <form action="/admin/maintenance_items/update" method="POST" class="ui form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{$item->id}}" name="id">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" id="title" value="{{$item->title}}">
            </div>
            <div class="form-group">
                <label for="role">Newsfeed Section</label>
                <select class="form-control" name="section" id="section">
                    <option selected="selected" disabled="disabled">--Choose a Section--</option>
                    @foreach (Custodia\Section::all() as $section)
                        <option
                                @if (isset($item->section))
                                @if ($section->id == $item->section->id)
                                selected="selected"
                                @endif
                                @endif
                                value="{{$section->id}}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="points">Points</label>
                <input type="number" name="points" class="form-control" id="points" value="{{$item->points}}">
            </div>
{{--            <div class="form-group">--}}
{{--                <label for="interval">Interval</label>--}}
{{--                <select class="form-control" name="interval" id="interval">--}}
{{--                    <option selected="selected" disabled="disabled">--Choose an Interval--</option>--}}
{{--                    @foreach (Custodia\Interval::all() as $interval)--}}
{{--                        <option--}}
{{--                                @if ($interval->id == $item->interval->id)--}}
{{--                                selected="selected"--}}
{{--                                @endif--}}
{{--                                value="{{$interval->id}}"--}}
{{--                                name="{{$interval->name}}">{{ $interval->name }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
            <div class="form-group" id="weather-trigger-form-group">
                <select class="form-control" name="trigger" id="trigger">
                    <option selected="selected" disabled="disabled">--Choose a Weather Trigger--</option>
                    @foreach (Custodia\WeatherTriggerType::all() as $trigger)
                        <option @if ($item->weather_trigger_type_id == $trigger->id) selected="selected"
                                @endif value="{{$trigger->id}}">{{ $trigger->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="mobility_priority">Mobility Priority</label>
                <select class="form-control" name="mobility_priority" id="mobility_priority">
                    <option value="0" @if($item->mobility_priority == false) selected="selected" @endif>No</option>
                    <option value="1" @if($item->mobility_priority) selected="selected" @endif>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <label for="home_types">Home Types</label>
                <div style="display: block;">
                    @foreach (\Custodia\HomeType::all() as $homeType)
                        <div class="ui checkbox" style="display: block;">
                            <input
                                    type="checkbox"
                                    name="home_types[]"
                                    value="{{$homeType->id}}"
                                    @if ($item->homeTypes->contains($homeType)) checked="checked" @endif
                            >
                            <label>{{$homeType->name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="home_type">Outdoor Spaces Applicable</label>
                <div style="display: block;">
                    @foreach (\Custodia\OutdoorSpaceType::all() as $spaceType)
                        <div class="ui checkbox" style="display: block;">
                            <input
                                    type="checkbox"
                                    name="outdoor_spaces[]"
                                    value="{{$spaceType->id}}"
                                    @if ($item->outdoorSpaces->contains($spaceType)) checked="checked" @endif
                            >
                            <label>{{$spaceType->name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="driveways">Driveways Applicable</label>
                <div style="display: block;">
                    @foreach (\Custodia\DrivewayType::all() as $drivewayType)
                        <div class="ui checkbox" style="display: block;">
                            <input
                                    type="checkbox"
                                    name="driveways[]"
                                    value="{{$drivewayType->id}}"
                                    @if ($item->drivewayTypes->contains($drivewayType)) checked="checked" @endif
                            >
                            <label>{{$drivewayType->name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="features">Home Features Applicable</label>
                <div style="display: block;">
                    @foreach (\Custodia\HomeFeature::all() as $featureType)
                        <div class="ui checkbox" style="display: block;">
                            <input
                                    type="checkbox"
                                    name="features[]"
                                    value="{{$featureType->id}}"
                                    @if ($item->homeFeatures->contains($featureType)) checked="checked" @endif
                            >
                            <label>{{$featureType->name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="mobility_issues">Mobility Issues Applicable</label>
                <div style="display: block;">
                    @foreach (\Custodia\MobilityIssueType::all() as $mobilityIssueType)
                        <div class="ui checkbox" style="display: block;">
                            <input
                                    type="checkbox"
                                    name="mobility_issues[]"
                                    value="{{$mobilityIssueType->id}}"
                                    @if ($item->mobilityIssues->contains($mobilityIssueType)) checked="checked" @endif
                            >
                            <label>{{$mobilityIssueType->name}}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group">
                <label for="summary">Summary</label>
                <textarea name="summary" class="form-control" id="summary" placeholder="Summary"
                          required>{{$item->summary}}</textarea>
            </div>
            <div class="form-group">
                <label for="cautions">Cautions</label>
                <textarea name="cautions" class="form-control" id="cautions" placeholder="Cautions"
                          required>{{$item->cautions}}</textarea>
            </div>
            <div class="form-group">
                <label for="video">Video</label>
                <input type="url" name="video" class="form-control" id="video" placeholder="Video"
                       value="{{$item->video}}">
            </div>
            <div class="tools">
                @foreach($item->tools as $tools)
                    <div class="form-group">
                        <label>Tool</label><br>
                        <input name="tools[]" class="form-control" placeholder="Tool" style="width: 88%;"
                               value="{{$tools->value}}">
                        <button class="delete-tool ui button negative" value="Remove">Remove</button>
                    </div>
                @endforeach
            </div>
            <div>
                <input type="button" class="add-tool ui button" value="Add Tool" style="margin-top: 10px;">
            </div>
            <div class="materials">
                @foreach($item->materials as $material)
                    <div class="form-group">
                        <label>Materials</label><br>
                        <input name="materials[]" class="form-control" placeholder="Materials" style="width: 88%;"
                               value="{{$tools->value}}">
                        <button class="delete-materials ui button negative" value="Remove">Remove</button>
                    </div>
                @endforeach
            </div>
            <div>
                <input type="button" class="add-materials ui button" value="Add Materials" style="margin-top: 10px;">
            </div>
            <div class="months-repeatable-container">
                @foreach ($item->months as $key => $month)
                    <div class="field-group" style="margin-bottom: 40px">
                        <div class="field-group" style="display: flex; margin-top: 10px;">
                            <select required class="form-control" name="months[{{$key}}][month]" id="month"
                                    style="margin-right: 5px;">
                                <option value="January" @if ($month->month == "January") selected="selected" @endif >
                                    January
                                </option>
                                <option value="February" @if ($month->month == "February") selected="selected" @endif >
                                    February
                                </option>
                                <option value="March" @if ($month->month == "March") selected="selected" @endif >March
                                </option>
                                <option value="April" @if ($month->month == "April") selected="selected" @endif >April
                                </option>
                                <option value="May" @if ($month->month == "May") selected="selected" @endif >May
                                </option>
                                <option value="June" @if ($month->month == "June") selected="selected" @endif >June
                                </option>
                                <option value="July" @if ($month->month == "July") selected="selected" @endif >July
                                </option>
                                <option value="August" @if ($month->month == "August") selected="selected" @endif >
                                    August
                                </option>
                                <option value="September"
                                        @if ($month->month == "September") selected="selected" @endif >
                                    September
                                </option>
                                <option value="October" @if ($month->month == "October") selected="selected" @endif >
                                    October
                                </option>
                                <option value="November" @if ($month->month == "November") selected="selected" @endif>
                                    November
                                </option>
                                <option value="December" @if ($month->month == "December") selected="selected" @endif>
                                    December
                                </option>
                            </select>
                            <select required class="form-control interval_repeatable" data-key="{{$key}}" name="months[{{$key}}][interval]">
                                <option selected="selected" disabled="disabled">--Choose an Interval--</option>
                                @foreach (\Custodia\Interval::all() as $interval)
                                    <option value="{{$interval->id}}" name="{{$interval->name}}"
                                            @if ($month->interval_id == $interval->id) selected="selected" @endif>{{ $interval->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="desc_photo">
                            @if($month->interval->name == 'Monthly')
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][0][text]" id="description" value="{{$month->monthsDescription[0]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][0][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[0]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][0][old_photos]" value="">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[0]->img_name)}}"/></div>                                      @endif
                                </div>
                            @elseif($month->interval->name == 'Biweekly')
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][0][text]" id="description" value="{{$month->monthsDescription[0]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][0][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[0]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][0][old_photos]" value="{{$month->monthsDescription[0]->img_name}}">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[0]->img_name)}}"/></div>                                      @endif
                                </div>
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][1][text]" id="description" value="{{$month->monthsDescription[1]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][1][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[1]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][1][old_photos]" value="{{$month->monthsDescription[1]->img_name}}">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[1]->img_name)}}"/></div>                                      @endif
                                </div>
                            @elseif($month->interval->name == 'Weekly')
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][0][text]" id="description" value="{{$month->monthsDescription[0]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][0][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[0]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][0][old_photos]" value="{{$month->monthsDescription[0]->img_name}}">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[0]->img_name)}}"/></div>                                      @endif
                                </div>
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][1][text]" id="description" value="{{$month->monthsDescription[1]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][1][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[1]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][1][old_photos]" value="{{$month->monthsDescription[1]->img_name}}">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[1]->img_name)}}"/></div>                                      @endif
                                </div>
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][2][text]" id="description" value="{{$month->monthsDescription[2]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][2][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[2]->img_name))
                                        <input required type="hidden" name="months[{{$key}}][descriptions][2][old_photos]" value="{{$month->monthsDescription[2]->img_name}}">
                                        <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[2]->img_name)}}"/></div>                                      @endif
                                </div>
                                <div class="field-group" style="display: flex; margin-top: 10px;">
                                    <input required type="text" name="months[{{$key}}][descriptions][3][text]" id="description" value="{{$month->monthsDescription[3]->description ?? ''}}">
                                    <input required id="photo" type="file" class="form-control"
                                           name="months[{{$key}}][descriptions][3][photos]" style="border:none;">
                                    @if (isset($month->monthsDescription[3]->img_name))
                                        <input type="hidden" name="months[{{$key}}][descriptions][3][old_photos]" value="{{$month->monthsDescription[3]->img_name}}">
                                     <div> <img style="max-width:62px;" src="{{asset($month->monthsDescription[3]->img_name)}}"/></div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <br>
                        <button class="delete ui button negative"
                                value="Remove">Remove
                        </button>
                        <hr>
                    </div>

                @endforeach
            </div>

            <input type="button" class="add ui button" value="Add Month" style="margin-top: 10px;"/>

            <!-- <div class="form-group">
                <label for="photo" >Photo</label>

            </div> -->
            <br>
            <button style="margin-top:30px;" type="submit" class="ui button primary">Save</button>

            <div class="" style="padding:30px;">

            </div>
        </form>
    </div>
@endsection


@section('scripts')
    <script>
        $(document).ready(function () {
            if ($('#interval').children("option:selected").attr('name') == "Weather Trigger") {
                $("#weather-trigger-form-group").show();
            } else {
                $("#weather-trigger-form-group").hide();
            }
        });

        $('#interval').on('change', function () {
            if ($(this).children("option:selected").attr('name') == "Weather Trigger") {
                $("#weather-trigger-form-group").show();
            } else {
                $("#weather-trigger-form-group").hide();
            }
        });
    </script>



    <script>
        $(document).ready(function () {
            row = "{{count($item->months) + 1}}";

            $('.add').click(function () {

                weekly = "       <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][0][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][0][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][1][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][1][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][2][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][2][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][3][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][3][photos]\" style=\"border:none;\">\n" +
                    "            </div>";
                biweekly = '     <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][0][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][0][photos]" style="border:none;">\n' +
                    '            </div>' +
                    '            <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][1][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][1][photos]" style="border:none;">\n' +
                    '            </div>';
                montly = '       <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][0][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][0][photos]" style="border:none;">\n' +
                    '            </div>';

                let html = '<div class="field-group" style="margin-bottom: 40px">\n' +
                    '            <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <select required class="form-control" name="months[' + row + '][month]" id="month" style="margin-right: 5px;">\n' +
                    '                    <option selected="selected" disabled="disabled">--Choose a Month--</option>\n' +
                    '                    <option value="January">January</option>\n' +
                    '                    <option value="February">February</option>\n' +
                    '                    <option value="March">March</option>\n' +
                    '                    <option value="April">April</option>\n' +
                    '                    <option value="May">May</option>\n' +
                    '                    <option value="June">June</option>\n' +
                    '                    <option value="July">July</option>\n' +
                    '                    <option value="August">August</option>\n' +
                    '                    <option value="September">September</option>\n' +
                    '                    <option value="October">October</option>\n' +
                    '                    <option value="November">November</option>\n' +
                    '                    <option value="December">December</option>\n' +
                    '                </select>\n' +
                    '                <select required class="form-control interval_repeatable" name="months['+row+'][interval]">\n' +
                    '                    <option selected="selected" disabled="disabled">--Choose an Interval--</option>\n' +
                    '                    @foreach (\Custodia\Interval::all() as $interval)' +
                    '                        <option value="{{$interval->id}}" name="{{$interval->name}}">{{ $interval->name }}</option>\n' +
                    '                    @endforeach\n' +
                    '                </select>\n' +
                    '            </div>\n' +
                    '            <div class="desc_photo">\n' +
                    '\n' +
                    '            </div><br>\n' +
                    '            <button class="delete ui button negative"\n' +
                    '                    value="Remove">Remove\n' +
                    '            </button>\n' +
                    '        <hr></div>';
                row++;
                $('.months-repeatable-container').append(html);
            });
            $(document).on('click', '.delete', function () {
                $(this).parent().remove();
            });
            $(document).on('change', '.interval_repeatable', function () {
              if($(this).attr('data-key')){
                  row = $(this).attr('data-key');
              }
                weekly = "       <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][0][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][0][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][1][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][1][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][2][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][2][photos]\" style=\"border:none;\">\n" +
                    "            </div>" +
                    "            <div class=\"field-group\" style=\"display: flex; margin-top: 10px;\">\n" +
                    "                <input required type=\"text\" name=\"months["+row+"][descriptions][3][text]\" id=\"description\">\n" +
                    "                <input required id=\"photo\" type=\"file\" class=\"form-control\" name=\"months["+row+"][descriptions][3][photos]\" style=\"border:none;\">\n" +
                    "            </div>";
                biweekly = '     <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][0][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][0][photos]" style="border:none;">\n' +
                    '            </div>' +
                    '            <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][1][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][1][photos]" style="border:none;">\n' +
                    '            </div>';
                montly = '       <div class="field-group" style="display: flex; margin-top: 10px;">\n' +
                    '                <input required type="text" name="months['+row+'][descriptions][0][text]" id="description">\n' +
                    '                <input required id="photo" type="file" class="form-control" name="months['+row+'][descriptions][0][photos]" style="border:none;">\n' +
                    '            </div>';
                let name = $(this).find(':selected').attr('name');
                $(this).parent().next('.desc_photo').html('');
                if(name == 'Weekly'){
                    $(this).parent().next('.desc_photo').append(weekly);
                }else if(name == 'Biweekly'){
                    $(this).parent().next('.desc_photo').append(biweekly);
                }else if(name == 'Monthly'){
                    $(this).parent().next('.desc_photo').append(montly);
                }else{
                    $(this).parent().next('.desc_photo').html('');
                }
            });

            $('.add-tool').click(function () {
                $('.tools').append('<div class="form-group">\n' +
                    '                    <label>Tool</label><br>\n' +
                    '                    <input name="tools[]" class="form-control" placeholder="Tool" style="width: 88%;">\n' +
                    '                    <button class="delete-tool ui button negative" value="Remove">Remove</button>\n' +
                    '                </div>');
            });
            $(document).on('click', '.delete-tool', function () {
                $(this).parent().remove();
            });
            $('.add-materials').click(function () {
                $('.materials').append('<div class="form-group">\n' +
                    '                    <label>Material</label><br>\n' +
                    '                    <input name="materials[]" class="form-control" placeholder="Material" style="width: 88%;">\n' +
                    '                    <button class="delete-materials ui button negative" value="Remove">Remove</button>\n' +
                    '                </div>');
            });
            $(document).on('click', '.delete-materials', function () {
                $(this).parent().remove();
            });
        });
    </script>
@endsection
