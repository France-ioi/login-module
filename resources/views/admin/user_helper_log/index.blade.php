@extends('layouts.admin')

@section('content')
    <p>
        {!! BootForm::open(['url' => '/admin/user_helper_log', 'method' => 'GET', 'class' => 'form-inline']) !!}
            <div class="form-group">
                <label>User ID</label>
                <input type="text" class="form-control input-sm" name="user_id" value="{{ request()->input('user_id') }}"/>
            </div>
            <div class="form-group">
                <label>Target user ID</label>
                <input type="text" class="form-control input-sm" name="target_user_id" value="{{ request()->input('target_user_id') }}"/>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Find</button>
        {!! BootForm::close() !!}
    </p>

    <hr/>

    @if(count($items))
        {{ $items->links() }}

        <table class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User ID</th>
                    <th>Target user ID</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->user_id }}</td>
                        <td>{{ $item->target_user_id }}</td>
                        <td>{{ $item->type }}</td>
                        <td>
                            <a href="{{ url('admin/user_helper_log/'.$item->id.'/details') }}" class="btn btn-xs btn-primary">Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    @endif
@endsection