@extends('layouts.popup')

@section('content')
        <div class="panel-body">
            <div class="sectionTitle">
                @lang('badge.header')
            </div>
            <table class="table">
                <tr>
                    <th></th>
                    <th>@lang('profile.header')</th>
                    <th>@lang('badge.badge')</th>
                </tr>
                <tr>
                    <td>@lang('profile.first_name')</td>
                    <td>{{ $user->first_name }}</th>
                    <td>{{ $badge_user['first_name'] }}</th>
                </tr>
                <tr>
                    <td>@lang('profile.last_name')</td>
                    <td>{{ $user->last_name }}</th>
                    <td>{{ $badge_user['last_name'] }}</th>
                </tr>
            </table>
            <p>@lang('badge.diff_notice')</p>
            {!! BootForm::open(['url' => '/badge/confirm_difference']) !!}
                {!! BootForm::checkbox('override_profile', trans('badge.override_checkbox')) !!}
                <div class="form-group">
                    <label>@lang('badge.comments')</label>
                    <textarea class="form-control" name="comments"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        @lang('ui.save')
                    </button>
                </div>
            {!! BootForm::close() !!}
        </div>
@endsection
