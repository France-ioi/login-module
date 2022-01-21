<h3>Official domain countries</h3>
<div class="row">
    @foreach($countries as $country)
        <div class="col-sm-4 col-xs-6">
            {!! BootForm::checkbox(
                'countries[]',
                $country->name,
                $country->id,
                isset($client_countries[$country->id])
            )!!}
        </div>
    @endforeach
</div>