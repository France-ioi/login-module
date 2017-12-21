<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Email;
use App\OAuthClient\Manager;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Profile\SchemaBuilder;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verificator;
use App\LoginModule\Migrators\Merge\Group;

class ProfileController extends Controller
{

    protected $context;
    protected $schema_builder;
    protected $profile;


    public function __construct(PlatformContext $context, SchemaBuilder $schema_builder, UserProfile $profile) {
        $this->context = $context;
        $this->schema_builder = $schema_builder;
        $this->profile = $profile;
    }


    public function index(Request $request) {
        $user = $this->profile->getUserBeforeEditor();

        if($badge_data = $this->context->badge()->restoreData()) {
            $user->fill($badge_data['user']);
        }

        $pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        if($pms_user) {
            if($redirect = $request->get('redirect_uri')) {
                $request->session()->put('url.intended', $request->get('redirect_uri'));
            }
        };
        $disabled = $this->disabledAttributes($pms_user, $user->login_fixed);

        $schema = $this->schema_builder->build(
            $user,
            $this->requiredAttributes(),
            $disabled,
            true//$request->has('all')
        );
        return view('profile.index', [
            'form' => [
                'model' => $user,
                'url' => '/profile',
                'method' => 'post',
                'files' => true,
                'id' => 'profile'
            ],
            'schema' => $schema,
            'toggle_optional_fields_allowed' => $schema->hasRequired(),
            'pms_redirect' => $pms_user,
            'cancel_url' => $this->context->cancelUrl(),
            'all' => $request->has('all'),
            'revalidation_fields' => Group::getRevalidationFields($user)
        ]);
    }


    public function update(Request $request, Verificator $verificator) {
        $user = $request->user();
        $pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        $schema = $this->schema_builder->build(
            $user,
            $this->requiredAttributes(),
            $this->disabledAttributes($pms_user, $user->login_fixed),
            $request->has('all')
        );
        //\DB::connection()->enableQueryLog();
        //dd($schema->rules());
        $this->validate($request, $schema->rules());

        if(($result = $this->profile->update($request, $schema->fillableAttributes())) !== true) {
            return redirect()->back()->withInput()->withErrors($result);
        }
        if(($result = $verificator->verify($user)) !== true) {
            return redirect()->back()->withInput()->withErrors($result);
        }
        $this->context->badge()->flushData();
        return redirect($this->context->continueUrl('/account'));
    }



    private function requiredAttributes() {
        if($client = $this->context->client()) {
            return $client->user_attributes;
        }
        return [];
    }


    private function disabledAttributes($pms_user, $login_fixed) {
        if($pms_user) {
            return Manager::provider('pms')->getFixedFields();
        }
        if($login_fixed) {
            return ['login'];
        }
        return [];
    }

}