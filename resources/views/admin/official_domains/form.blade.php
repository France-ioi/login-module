@extends('layouts.admin')

@section('content')
    <h2>
        @if($official_domain->exists())
            Domain ID: {{ $official_domain->id }}
        @else
            New domain
        @endif
    </h2>
    {!! BootForm::open(['model' => $official_domain, 'store' => 'admin.official_domains.store', 'update' => 'admin.official_domains.update']) !!}
        {!! BootForm::text('domain', 'Domain') !!}
        {!! BootForm::select('country_id', 'Country', $countries) !!}
        {!! BootForm::submit() !!}
    {!! BootForm::close() !!}
@endsection
