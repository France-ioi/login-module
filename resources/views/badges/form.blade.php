{!! BootForm::open(['url' => '/badges/add']) !!}
    {!! BootForm::select('badge_api_id', trans('badges.select_badge_api'), $available) !!}
    {!! BootForm::text('code', trans('badges.code')) !!}
    {!! BootForm::submit(trans('badge.btn_verify_code')) !!}
{!! BootForm::close() !!}