<?php

namespace App\LoginModule\Profile;

use App\LoginModule\Platform\PlatformContext;
use App\OAuthClient\Manager;
use Illuminate\Http\Request;


class ProfileForm {


    public function __construct(PlatformContext $context, BootFormRenderer $renderer) {
        $this->context = $context;
        $this->renderer = $renderer;
    }


    public function render($request) {
        $user = $this->userModel($request->user());
        $schema = new Schema(
            $this->requiredAttributes(),
            $this->disabledAttributes($request),
            $user
        );
        return $this->renderer->render(
            $this->formOptions($request->url(), $user),
            $schema
        );
    }


    private function userModel($user) {
        if($badge_data = $this->context->badge()->restoreData()) {
            $user->fill($badge_data['user']);
        }
        return $user;
    }


    private function requiredAttributes() {
        if($client = $this->context->client()) {
            return $client->user_attributes;
        }
        return [];
    }


    private function disabledAttributes($request) {
        $res = [];
        if($request->user()->auth_connections()->where('provider', 'pms')->where('active', '1')->first()) {
            $res = Manager::provider('pms')->getFixedFields();
            if($redirect = $request->get('redirect_uri')) {
                // Redirect to callback_profile from the platform after showing the dialog
                $request->session()->put('url.intended', $request->get('redirect_uri'));
            }
        }
        return $res;
    }


    private function formOptions($url, $model) {
        return [
            'method' => 'post',
            'model' => $model,
            'url' => $url
        ];
    }

}