@component('mail::layout')
@slot('header')
@component('mail::header', ['url' => $app_url])
{{ $app_name }}
@endcomponent
@endslot

{{ $body }}

@slot('subcopy')
@isset($subcopy)
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endisset
@endslot


@slot('footer')
@isset($footer)
@component('mail::footer')
{{ $footer }}
@endcomponent
@endisset
@endslot
@endcomponent