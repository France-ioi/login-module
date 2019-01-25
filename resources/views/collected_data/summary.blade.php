<table class="table">
    @foreach($data as $item)
        <tr>
            <td class="name">{{$item->title}}</td>
            <td class="value">{{$item->value}}</td>
        </tr>
    @endforeach
</table>
