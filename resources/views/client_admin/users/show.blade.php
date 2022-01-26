@extends('layouts.client_admin')

@section('content')
    <h3>User {{ $user->id }}</h3>
    <p>{{ $user->first_name }} {{ $user->last_name }}</p>

    {!! BootForm::open(['url' => '/client_admin/'.$client->id.'/users/'.$user->id.'/verify']) !!}
        <input type="hidden" name="refer_page" value="{{ $refer_page }}"/>
        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Value</th>
                    <th>Verify</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->verifiable_attributes as $attr)
                    <tr>
                        <td>{{ $attr }}</td>
                        <td>{{ $user->getAttribute($attr) }}</td>
                        <td><input type="checkbox" name="verified_attributes[{{ $attr }}]"/></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Create manual verification</button>
        <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
    {!! BootForm::close() !!}
@endsection