<div class="panel panel-default">
    <div class="panel-heading">User #{{ $user->id}}</div>
    <div class="panel-body">
        <div>{{ $user->login }}</div>
        <div>{{ $user->first_name }} {{ $user->last_name }}</div>
        <div>{{ $user->primary_email }}</div>
        <div>{{ $user->secondary_email }}</div>
    </div>
</div>