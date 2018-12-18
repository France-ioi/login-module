@extends('layouts.admin')

@section('content')
    <h3>Verification</h3>
    @include('admin.users.user_info', $user)

    {!! BootForm::open(['/admin/user_helper/'.$user->id.'/verification', 'method' => 'POST' ]) !!}
        <table class="table">
            <tr>
                <th>Attribute</th>
                <th>Value</th>
                <th>Verified</th>
            </tr>

            @foreach($user_helper->verifiable_attributes as $attr)
                <tr>
                    <td>@lang('profile.'.$attr)</td>
                    <td>{{ $user->getAttribute($attr) }}</td>
                    <td>
                        {!! BootForm::checkbox(
                            'verified[]',
                            ' ',
                            $attr,
                            in_array($attr, $verified_attributes)
                        ) !!}
                    </td>
                </tr>
            @endforeach
        </table>
        {!! BootForm::submit('Save') !!}
    {!! BootForm::close() !!}
@endsection