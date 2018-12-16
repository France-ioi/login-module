@extends('layouts.admin')

@section('content')
    <pre>{!! json_encode($item->details, JSON_PRETTY_PRINT) !!}</pre>
@endsection