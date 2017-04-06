@extends('layouts.popup')
    
@section('content')
    <div class="alert alert-warning">
        @lang('auth.session_expired')
    </div>
@endsection
