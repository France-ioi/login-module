@extends('layouts.client_admin')

@section('content')
    @include('client_admin.user_card')


    <div class="alert alert-info">To process user account deletion you must be logged in as platfrom admin on target platform.</div>

    <table class="table" id="platforms-table">
        <tr>
            <th class="col-xs-6">Target platform</th>
            <th class="col-xs-6">Action / Status</th>
        </tr>
        @foreach($user_clients as $uc)
            <tr>
                <td>
                    {{ $uc->name }}
                </td>
                <td>
                    @if($uc->pivot->deleted)
                        Deleted
                    @else
                        {!! BootForm::horizontal([
                            'url' => route('client_admin.user_delete_redirect', [
                                'client_id' => $client->id,
                                'user_id' => $user->id 
                            ]
                        )]) !!}
                            {!! BootForm::hidden('target_client_id', $uc->id) !!}
                            <button type="submit" class="btn btn-danger btn-xs">Delete account</button>
                        {!! BootForm::close() !!}                    
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td>Login module</td>
            <td>
                @if($lm_delete_available)
                    {!! BootForm::horizontal() !!}
                        <button type="submit" class="btn btn-danger btn-xs">Delete account</button>
                    {!! BootForm::close() !!}
                @else
                    Delete not available until user account exists at other platforms.
                @endif
            </td>
        </tr>
    </table>


    <a class="btn btn-primary" href="{{ route('client_admin.users', $client->id) }}">Back</a>


    <script>
        $('#platforms-table').find('button[type="submit"]').on('click', function(e) {
            if(!confirm('This will delete user data. Continue?')) {
                e.preventDefault();
            }
        })
    </script>
@endsection