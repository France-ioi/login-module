@extends('layouts.popup')

@section('header')
@endsection

@section('content')
    <div class="panel-body">
        <div class="sectionTitle">
            <i class="fas fa-key icon"></i>
            @lang('Modification du mot de passe')
        </div>
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <p><b>Modification du mot de passe de l'utilisateur {{ $user->login }}</b></p>

        {!! BootForm::horizontal() !!}
            {!! BootForm::password('password', trans('password.pwd_new'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
            {!! BootForm::password('password_confirmation', trans('auth.pwd_confirm'), ['prefix' => BootForm::addonIcon('key fas')]) !!}
            <div class="form-group">
                <button type="submit" class="btn btn-rounded btn-primary btn-centered"><i class="fas fa-check icon"></i>Valider</button>
                <a type="button" class="btn btn-rounded btn-default btn-centered" href="{{ $return_url }}"><i class="fas fa-times icon"></i>Retour</a>
            </div>
        {!! BootForm::close() !!}
        </div>
@endsection
