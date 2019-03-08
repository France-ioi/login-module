@extends('layouts.popup')

@section('content')
        <div class="panel-heading">
            @lang('merging_accounts.header')
        </div>
        <div class="panel-body">
            {{ \Session::get('merge_account_id' )}}
            <p>
                @lang('merging_accounts.intoduction', ['instance_name' => $instance_name])
            </p>

            <ul>
                @foreach($similar_fields as $field)
                    <li>{{ $field }}</li>
                @endforeach
            </ul>

            <p>
                @lang('merging_accounts.confirmation')
                {!! BootForm::open(['url' => '/merging_accounts/accept', 'method' => 'post']) !!}
                    {!! BootForm::submit(trans('merging_accounts.btn_yes')) !!}
                {!! BootForm::close() !!}

                {!! BootForm::open(['url' => '/merging_accounts/decline', 'method' => 'post']) !!}
                    {!! BootForm::submit(trans('merging_accounts.btn_no', ['instance_name' => $instance_name])) !!}
                {!! BootForm::close() !!}
            </p>
        </div>
@endsection