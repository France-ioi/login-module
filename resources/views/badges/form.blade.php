{!! BootForm::open(['url' => '/badges/add']) !!}
    {!! BootForm::select('badge_api_id', trans('badges.select_badge_api'), $available) !!}
    {!! BootForm::text('code', trans('badges.code')) !!}
    <div class="form-group">
        <button type="submit" class="btn btn-rounded btn-primary pull-right"><i class="fas fa-check icon"></i>@lang('badge.btn_verify_code')</button>
    </div>
{!! BootForm::close() !!}