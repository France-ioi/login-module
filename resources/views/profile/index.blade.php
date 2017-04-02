@extends('layouts.popup')

@section('content')
    @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('profile.header')
        </div>
        <div class="panel-body">
            {!! BootForm::open(['url' => '/profile', 'method' => 'post', 'model' => $user]) !!}
                @if($all)
                    {!! BootForm::hidden('all', 1) !!}
                @endif
                @include('profile.fields')
                @foreach($fields as $field)
                    @stack($field)
                @endforeach
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('ui.save')
                    </button>
                    @if($cancel_url)
                        <a class="btn btn-link" href="{{ $cancel_url }}">
                            @lang('ui.close')
                        </a>
                    @endif
                </div>
            {!! BootForm::close() !!}
        </div>
    </div>

    <link href="/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function() {
            $('#birthday').datepicker({
                format: 'yyyy-mm-dd',
                endDate: new Date(),
                autoclose: true
            });

            function updateHidden() {
                $('#box_ministry_of_education_fr').hide();
                $('#box_ministry_of_education').hide();
                var country_code = $('#country_code').val();
                if(country_code == 'FR') {
                    $('#box_ministry_of_education_fr').show();
                } else {
                    $('#box_ministry_of_education').show();
                }
            }
            $('#country_code').change(updateHidden);
            updateHidden();
        })
    </script>
@endsection