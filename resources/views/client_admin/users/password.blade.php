@extends('layouts.client_admin')

@section('content')
    @include('client_admin.users.user_card')

    {!! BootForm::open(['url' => '/client_admin/'.$client->id.'/users/'.$user->id.'/password']) !!}
        <input type="hidden" name="refer_page" value="{{ $refer_page }}"/>
        {!! BootForm::text('password', 'New password') !!}
        <button type="submit" class="btn btn-primary">Change password</button>
    </form>
@endsection