@extends('layouts.popup')

@section('tabs_menu')
@endsection

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">

            @lang('passport.authorization_header')
        </div>
        <div class="panel-body passport-authorize">
            <!-- Introduction -->
            <p>
                <strong>{{ $client->name }}</strong>
                @lang('passport.intoduction')
            </p>

            <!-- Scope List -->
            @if (count($scopes) > 0)
                <div class="scopes">
                        <p><strong>@lang('passport.scopes_title')</strong></p>
                        <ul>
                            @foreach ($scopes as $scope)
                                <li>@lang('passport.scopes.'.$scope->description)</li>
                            @endforeach
                        </ul>
                </div>
            @endif

            <div class="buttons">
                <!-- Authorize Button -->
                <form method="post" action="/oauth/authorize">
                    {{ csrf_field() }}

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <button type="submit" class="btn btn-success btn-approve">@lang('passport.btn_authorize')</button>
                </form>

                <!-- Cancel Button -->
                <form method="post" action="/oauth/authorize">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <button class="btn btn-danger">@lang('ui.cancel')</button>
                </form>
            </div>
        </div>
    </div>
@endsection