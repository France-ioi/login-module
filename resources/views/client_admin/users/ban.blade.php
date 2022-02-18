@extends('layouts.client_admin')

@section('content')
    @include('client_admin.users.user_card')

    {!! BootForm::open(['url' => '/client_admin/'.$client->id.'/users/'.$user->id.'/ban']) !!}
        <input type="hidden" name="refer_page" value="{{ $refer_page }}"/>
        {!! BootForm::select('banned', 'Status', ['0' => 'Not banned', '1' => 'Banned'], $banned) !!}
        <button type="submit" class="btn btn-primary">Save</button>
        <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
    {!! BootForm::close() !!}
@endsection