<?php

//TOFO: replace with prev version

namespace App\LoginModule\Profile;

use Illuminate\Contracts\Routing\ResponseFactory;
use App\LoginModule\Platform\PlatformContext;
use App\OAuthClient\Manager;

use Validator;
use Verification\Verificator;
use Request;

class UserProfile {

    protected $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function update($request, $fillable_attributes) {
        $user = $request->user();
        $data = $request->only($fillable_attributes);
        $user->fill($data);
        $user->save();
    }


    public function completed() {
        return false;
    }


    public function verified() {
        return false;
    }

}