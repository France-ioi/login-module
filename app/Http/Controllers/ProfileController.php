<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Email;
use App\OAuthClient\Manager;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Profile\SchemaBuilder;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verification;
use App\LoginModule\Migrators\Merge\Group;
use Carbon\Carbon;

class ProfileController extends Controller
{

    protected $context;
    protected $schema_builder;
    protected $profile;
    protected $verification;


    public function __construct(PlatformContext $context,
                                SchemaBuilder $schema_builder,
                                UserProfile $profile,
                                Verification $verification) {

        $this->context = $context;
        $this->schema_builder = $schema_builder;
        $this->profile = $profile;
        $this->verification = $verification;
    }


    public function index(Request $request) {
        $user = $this->profile->getUserBeforeEditor();

        $is_pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        if($is_pms_user) {
            if($redirect = $request->get('redirect_uri')) {
                $request->session()->put('url.intended', $request->get('redirect_uri'));
            }
        };
        $disabled = $this->disabledAttributes($user, $is_pms_user, $user->login_fixed);

        $client = $this->context->client();
        $required_attributes = $this->requiredAttributes($user);
        $recommended_attributes = $client ? $client->recommended_attributes : [];
        if($user->login_change_required) {
            $required_attributes = ['login'];
            $recommended_attributes = [];
        }
        $schema = $this->schema_builder->build(
            $user,
            $required_attributes,
            $recommended_attributes,
            $disabled,
            true//$request->has('all')
        );

        $unverified_attributes = $this->verification->unverifiedAttributes($user);
        $verification_ready = $this->profile->attributesCompleted($user, $unverified_attributes);

        return view('profile.index', [
            'form' => [
                'model' => $user,
                'url' => '/profile',
                'method' => 'post',
                'files' => true,
                'id' => 'profile'
            ],
            'schema' => $schema,
            'pms_redirect' => $is_pms_user,
            'cancel_url' => $this->context->cancelUrl(),
            'all' => $request->has('all') || count($required_attributes) == 0,
            'revalidation_fields' => Group::getRevalidationFields($user),
            'unverified_attributes' => $unverified_attributes,
            'verification_ready' => $verification_ready,
            'verified_attributes' => $this->verification->verifiedAttributes($user)
        ]);
    }


    public function update(Request $request) {
        $user = $request->user();

        $is_pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        $required_attributes = $this->requiredAttributes($user);
        if($user->login_change_required) {
            $required_attributes = ['login'];
        }
        $schema = $this->schema_builder->build(
            $user,
            $required_attributes,
            [],
            $this->disabledAttributes($user, $is_pms_user, $user->login_fixed),
            true
        );
        //\DB::connection()->enableQueryLog();
        //dd($schema->rules());
        $this->validate($request, $schema->rules());

        $this->clearVerifications($user, $request);

        if(($result = $this->profile->update($request, $schema->fillableAttributes())) !== true) {
            return redirect()->back()->withInput()->withErrors($result);
        }
        return redirect($this->context->continueUrl('/account'));
    }


    private function clearVerifications($user, $request) {
        $verified_attributes = $this->verification->verifiedAttributes($user);
        $res = [];
        foreach($verified_attributes as $attr) {
            if($user->getAttribute($attr) != $request->get($attr)) {
                $res[] = $attr;
            }
        }
        Verification::clear($user, $res);
    }


    private function requiredAttributes($user) {
        if($client = $this->context->client()) {
            return $this->profile->getRequiredUserAttributes($user, $client);
            //return $client->user_attributes;
        }
        return [];
    }


    private function disabledAttributes($user, $is_pms_user, $login_fixed) {
        if($is_pms_user) {
            return Manager::provider('pms')->getFixedFields();
        }
        $login_change_restricted = false;
        if(!is_null($user->login) && !is_null($user->login_updated_at) && $login_change_available = config('profile.login_change_available')) {
            $first = (new \DateTime($user->login_updated_at))->add(new \DateInterval($login_change_available['first_interval']));
            $second = (new \DateTime($user->login_updated_at))->add(new \DateInterval($login_change_available['second_interval']));
            $now = new \DateTime;
            $login_change_restricted = $now > $first && $now < $second;
        }
        if($login_fixed || $login_change_restricted) {
            return ['login'];
        }
        return [];
    }


}