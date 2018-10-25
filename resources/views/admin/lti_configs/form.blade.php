@extends('layouts.admin')

@section('content')
    <h2>
        @if($lti_config->exists())
            Consumer key: {{ $lti_config->lti_consumer_key }}
        @else
            New config
        @endif
    </h2>

    {!! BootForm::open(['model' => $lti_config, 'store' => 'admin.lti_configs.store', 'update' => 'admin.lti_configs.update']) !!}
        {!! BootForm::text('lti_consumer_key') !!}
        {!! BootForm::text('prefix') !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}

@endsection
