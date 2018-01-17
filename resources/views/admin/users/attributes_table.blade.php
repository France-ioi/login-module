<table class="table">
    <tr>
        <th>Attribute</th>
        <th>Value</th>
    </tr>
    @foreach($model->getAttributes() as $key => $value)
        <tr>
            <td>{{ $key }}</td>
            <td>{{ $value }}</td>
        </tr>
    @endforeach
</table>