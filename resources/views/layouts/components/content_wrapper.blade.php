@if(Auth::check())
    <div class="panel panel-default">
@else
    <div class="panel panel-auth">
@endif
@yield('content')
</div>