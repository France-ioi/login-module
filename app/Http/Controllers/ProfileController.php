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
use App\LoginModule\Profile\ProfileFilter;
use App\LoginModule\LoginSuggestion;

class ProfileController extends Controller
{

    protected $context;
    protected $schema_builder;
    protected $profile;
    protected $verification;


    public function __construct(PlatformContext $context,
                                SchemaBuilder $schema_builder,
                                UserProfile $profile,
                                Verification $verification,
                                LoginSuggestion $login_suggestion) {
        $this->context = $context;
        $this->schema_builder = $schema_builder;
        $this->profile = $profile;
        $this->verification = $verification;
        $this->login_suggestion = $login_suggestion;
    }


    public function index(Request $request, ProfileFilter $profile_filter) {
        $user = $this->profile->getUserBeforeEditor();

        $is_pms_user = (bool) $user->authConnections()->where('provider', 'pms')->where('active', '1')->first();
        if($is_pms_user) {
            if($redirect = $request->get('redirect_uri')) {
                $request->session()->put('url.intended', $request->get('redirect_uri'));
            }
        };
        $disabled_attributes = $this->disabledAttributes($user, $is_pms_user, $user->login_fixed);

        $client = $this->context->client();
        $required_attributes = $this->requiredAttributes($user);
        $recommended_attributes = $client ? $client->recommended_attributes : [];
        if($user->login_change_required) {
            $required_attributes = ['login'];
            $recommended_attributes = [];
        }
        $unverified_attributes = $this->verification->unverifiedAttributes($user);
        $verification_ready = $this->profile->attributesCompleted($user, $unverified_attributes);

        $schema = $this->schema_builder->build(
            $user,
            $required_attributes,
            $recommended_attributes,
            $disabled_attributes,
            $unverified_attributes,
            $this->hiddenAttributes($user)
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
            'pms_redirect' => $is_pms_user,
            'cancel_url' => $this->context->cancelUrl(),
            'optional_fields_visible' => $request->has('optional_fields_visible') || count($required_attributes) == 0,
            'revalidation_fields' => Group::getRevalidationFields($user),
            'unverified_attributes' => $unverified_attributes,
            'verification_ready' => $verification_ready,
            'verified_attributes' => $this->verification->verifiedAttributes($user),
            'show_email_verification_alert' => $this->emailVerificationAvailable($user),
            'platform_name' => $client ? $client->name : trans('app.name'),
            'rejected_attributes' => $profile_filter->rejectedAttributes($user),
            'profile_completed' => $this->profile->completed($user),
            'login_validator' => config('profile.login_validator'),
            'suggested_login' => $request->session()->get('suggested_login')
        ]);
    }


    public function update(Request $request) {
        $user = $request->user();
        $login = $request->get('login');
        if($user->login !== $login) {
            $suggested_login = $this->login_suggestion->get($login);
            return redirect()->back()->withInput()->with('suggested_login', $suggested_login);
        }

        $is_pms_user = (bool) $user->authConnections()->where('provider', 'pms')->where('active', '1')->first();
        $required_attributes = $this->requiredAttributes($user);
        if($user->login_change_required) {
            $required_attributes = ['login'];
        }
        $schema = $this->schema_builder->build(
            $user,
            $required_attributes,
            [],
            $this->disabledAttributes($user, $is_pms_user, $user->login_fixed)
        );
        //\DB::connection()->enableQueryLog();
        //dd($schema->rules());
        $this->validate($request, $schema->rules());

        $this->clearVerifications($user, $request);

        if(($result = $this->profile->update($request, $schema->fillableAttributes())) !== true) {
            return redirect()->back()->withInput()->withErrors($result);
        }
        return redirect($this->context->continueUrl());
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


    private function hiddenAttributes($user) {
        if($client = $this->context->client()) {
            $res = [];
            foreach($client->hidden_attributes as $attr) {
                $v = $user->getAttribute($attr);
                if($v == '' || $v === null) {
                    $res[] = $attr;
                }
            }
            return $res;
        }
        return [];
    }


    private function disabledAttributes($user, $is_pms_user, $login_fixed) {
        $res = [];
        if($is_pms_user) {
            $res =  array_merge(
                $res,
                Manager::provider('pms')->getFixedFields()
            );
        }
        $login_change_restricted = false;
        if(!is_null($user->login) && !is_null($user->login_updated_at) && $login_change_available = config('profile.login_change_available')) {
            $first = (new \DateTime($user->login_updated_at))->add(new \DateInterval($login_change_available['first_interval']));
            $second = (new \DateTime($user->login_updated_at))->add(new \DateInterval($login_change_available['second_interval']));
            $now = new \DateTime;
            $login_change_restricted = $now > $first && $now < $second;
        }
        if($login_fixed || $login_change_restricted) {
            $res[] = 'login';
        }
        $gdpr = config('gdpr.attributes');
        foreach($gdpr as $field) {
            if(!$user->$field) {
                $res[] = $field;
            }
        }
        return array_unique($res);
    }



    private function emailVerificationAvailable($user) {
        $attributes = ['primary_email', 'secondary_email'];
        foreach($attributes as $attr) {
            $completed = $this->profile->attributesCompleted($user, [$attr]);
            $verified = $this->verification->attributeVerifiedGlobal($user, $attr);
            if($completed && !$verified) {
                return true;
            }
        }
        return false;
    }


}