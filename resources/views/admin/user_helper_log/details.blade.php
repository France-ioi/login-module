@extends('layouts.admin')

@section('content')
    <table class="table">
        <tr>
            <td>Timestamp</td>
            <td>{{$item->created_at}}</td>
        </tr>
        <tr>
            <td>User ID</td>
            <td>{{$item->user_id}}</td>
        </tr>
        @if($item->target_user_id)
            <tr>
                <td>Target user ID</td>
                <td>{{$item->target_user_id}}</td>
            </tr>
        @endif
        <tr>
            <td>Type</td>
            <td>{{$item->type}}</td>
        </tr>
    </table>
    <h3>Details</h3>
    <pre>{!! json_encode($item->details, JSON_PRETTY_PRINT) !!}</pre>
@endsection