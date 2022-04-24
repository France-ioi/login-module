@extends('layouts.client_admin')

@section('content')
    @include('client_admin.user_card')

    {!! BootForm::open(['url' => '/client_admin/'.$client->id.'/users/'.$user->id.'/verification']) !!}
        <input type="hidden" name="refer_page" value="{{ $refer_page }}"/>
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Value</th>
                    <th>Verification required</th>
                    <th>User verification</th>
                    <th>Admin verification</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attributes as $attr)
                    <tr>
                        <td>{{ $attr }}</td>
                        <td>{{ $user->getAttribute($attr) }}</td>
                        <th>{{ isset($verification_required[$attr]) ? 'Yes' : '' }}</th>
                        <th>{{ isset($verified_attributes[$attr]) ? 'Yes' : '' }}</th>                        
                        <td><input type="checkbox" name="admin_verified[{{ $attr }}]" {{ isset($admin_verified[$attr]) ? 'checked="checked"' : ''}}/></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save</button>
        <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
    {!! BootForm::close() !!}
@endsection