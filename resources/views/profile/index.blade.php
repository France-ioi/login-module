@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">Profile</div>
        <div class="panel-body">
            <form role="form" method="POST" action="{{ url('profile') }}">
            {!! BootForm::openHorizontal(['xs' => [4, 8]])->action(url('/profile'))->post() !!}
                {{ csrf_field() }}

                @if(isset($fields['primary_email']))
                    {!! BootForm::email('primary_email') !!}
                @endif

                @if(isset($fields['secondary_email']))
                    {!! BootForm::email('secondary_email') !!}
                @endif

                @if(isset($fields['first_name']))
                    {!! BootForm::text('First name', 'first_name') !!}
                @endif

                @if(isset($fields['last_name']))
                    {!! BootForm::text('Last name', 'last_name') !!}
                @endif

                {!! BootForm::submit(trans('auth.btn_save')) !!}
            {!! BootForm::close() !!}
        </div>
    </div>
@endsection