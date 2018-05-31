@extends('layouts.popup')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('collected_data.item_self')
        </div>
        <table class="table table-bordered">
            @foreach($data as $item)
                <tr>
                    <td>{{$item->title}}</td>
                    <td>{{$item->value}}</td>
                </tr>
            @endforeach
        </table>
    </div>

    <a class="btn btn-default" href="/collected_data">@lang('ui.close')</a>
@endsection