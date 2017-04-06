@extends('layouts.popup')

@section('content')
    <div class="alert alert-info">
        @lang('profile.pms_redirect_msg')
    </div>

    <a class="btn btn-block btn-default" href="/oauth_client/preferences/pms">
        @lang('profile.pms_redirect_btn')
    </a>
@endsection
