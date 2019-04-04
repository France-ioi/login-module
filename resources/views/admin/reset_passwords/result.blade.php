@extends('layouts.admin')

@section('content')
    <table class="table">
        <tr>
            <th>User ID</th>
            <th>User login</th>
            <th>User password</th>
        </tr>
        @foreach($data as $row)
            <tr>
                <td>{{$row['id']}}</td>
                <td>{{$row['login']}}</td>
                <td>{{$row['password']}}</td>
            </tr>
        @endforeach
    </table>
@endsection