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
use Carbon\Carbon;

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
        //dd($user);

        if($badge_data = $this->context->badge()->restoreData()) {
            foreach($badge_data['user'] as $k => $v) {
                if($v) $user->$k = $v;
            }
        }

        $is_pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        if($is_pms_user) {
            if($redirect = $request->get('redirect_uri')) {
                $request->session()->put('url.intended', $request->get('redirect_uri'));
            }
        };
        $disabled = $this->disabledAttributes($user, $is_pms_user, $user->login_fixed);

        $client = $this->context->client();
        $schema = $this->schema_builder->build(
            $user,
            $this->requiredAttributes($user),
            $client ? $client->recommended_attributes : [],
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
            'has_optional_fields' => $schema->hasOptionalAttributes(),
            'pms_redirect' => $is_pms_user,
            'cancel_url' => $this->context->cancelUrl(),
            'all' => $request->has('all'),
            'revalidation_fields' => Group::getRevalidationFields($user),
            'login_change_required' => $user->login_change_required
        ]);
    }


    public function update(Request $request, Verificator $verificator) {
        $user = $request->user();
        $is_pms_user = (bool) $user->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
        $schema = $this->schema_builder->build(
            $user,
            $this->requiredAttributes($user),
            [],
            $this->disabledAttributes($user, $is_pms_user, $user->login_fixed),
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