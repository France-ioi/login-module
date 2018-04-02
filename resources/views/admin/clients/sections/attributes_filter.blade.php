<h3>Attributes filter</h3>
<div class="row">
    @foreach($user_attributes as $attr)
        <div class="col-sm-4 col-xs-6">
            {!! BootForm::text(
                'attributes_filter['.$attr.']',
                $attr,
                isset($client->attributes_filter[$attr]) ? $client->attributes_filter[$attr] : ''
            )!!}
        </div>
    @endforeach
</div>