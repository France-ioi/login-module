<h3>Required user attributes</h3>
<div class="row">
    @foreach($user_attributes as $attr)
        <div class="col-sm-4 col-xs-6">
            {!! BootForm::checkbox(
                'user_attributes[]',
                $attr,
                $attr,
                in_array($attr, $client->user_attributes)
            )!!}
        </div>
    @endforeach
</div>


<h3>Recommended user attributes</h3>
<div class="row">
    @foreach($user_attributes as $attr)
        <div class="col-sm-4 col-xs-6">
            {!! BootForm::checkbox(
                'recommended_attributes[]',
                $attr,
                $attr,
                in_array($attr, $client->recommended_attributes)
            )!!}
        </div>
    @endforeach
</div>