@extends('layouts.admin')

@section('content')
    <h2>
        @if($origin_instance->exists())
            Domain ID: {{ $origin_instance->id }}
        @else
            New instance
        @endif
    </h2>
    {!! BootForm::open(['model' => $origin_instance, 'store' => 'admin.origin_instances.store', 'update' => 'admin.origin_instances.update']) !!}
        {!! BootForm::text('name', 'Name') !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection
