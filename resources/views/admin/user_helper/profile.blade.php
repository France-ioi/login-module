@extends('layouts.admin')

@section('content')
    <h3>User profile</h3>

    @include('admin.users.user_info', $user)

    {!! BootForm::open(['/admin/user_helper/'.$user->id.'/password', 'model' => $user, 'method' => 'POST' ]) !!}
        @include('admin.user_helper.user_attributes')
        @foreach($user_helper->user_attributes as $attr => $permission)
            @if($permission == 'write')
                @stack($attr)
            @elseif($permission == 'read')
                <div class="form-group">
                    <label class="control-label">
                        @lang('profile.'.$attr)
                    </label>
                    <p class="form-control-static">
                        {{ $user->getAttribute($attr) }}
                    </p>
                  </div>
            @endif
        @endforeach
        {!! BootForm::submit('Save') !!}
    {!! BootForm::close() !!}

    <link href="/css/bootstrap-datepicker3.css" rel="stylesheet">
    <script type="text/javascript">
        $(document).ready(function() {
            $('#birthday').datepicker({
                format: 'yyyy-mm-dd',
                endDate: new Date(),
                autoclose: true,
                language: '{!! app()->getLocale() !!}'
            });
        })
    </script>
@endsection