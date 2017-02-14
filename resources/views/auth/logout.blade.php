@extends('layouts.app')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">@lang('auth.logout_header')</div>
        <div class="panel-body">
            <form role="form" method="POST" action="{{ route('logout') }}">
                {{ csrf_field() }}
                <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}"?>
                @if(count($active_connections) > 0)
                    <p><strong>
                        @lang('auth.connections_title')
                    </strong></p>
                    @foreach($active_connections as $connection)
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="connection{{ $connection->id }}" checked="checked" disabled="disabled"> {{ $connection->provider }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('auth.btn_logout')
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection