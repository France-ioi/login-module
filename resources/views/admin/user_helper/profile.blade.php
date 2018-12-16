@extends('layouts.admin')

@section('content')
    @include('admin.users.user_info', $user)
    @include('admin.user_helper.user_attributes')
    {!! BootForm::open(['/admin/user_helper/'.$user->id.'/password', 'model' => $user ]) !!}
        @foreach($user_helper->user_attributes as $attr => $permission)
            @if($permission == 'write')
                @stack($attr)
            @elseif($permission == 'read')
                <div class="form-group">
                    <label class="control-label">
                        @lang('profile.'.$attr)
                    </label>
                    <p class="form-control-static">
                        {{ $user->getAttribute($attr) }}
                    </p>
                  </div>
            @endif
        @endforeach
        {!! BootForm::submit('Save') !!}
    {!! BootForm::close() !!}
@endsection