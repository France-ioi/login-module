<?php

namespace App\Http\Controllers\UserAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Profile\Verification\Verification;

class AccountController extends Controller
{

    public function __construct(PlatformContext $context, Verification $verification) {
        $this->context = $context;
        $this->verification = $verification;
    }

    public function show(Request $request) {
        $user = $request->user();
        $client_id = $user->token()->client_id;
        $res = $user->toArray();
        $res['badges'] = $this->getBadges($request->user());
        $res['client_id'] = $client_id;
        $platform_group = $request->user()->platformGroups()->where('client_id', $client_id)->first();
        if($platform_group) {
            $res['platform_group_code'] = $platform_group->group_code;
            $platform_group->delete();
        }
        $this->context->setClientId($client_id);
        $res['verification'] = $this->verification->attributesState($request->user());
        $res['client_admin'] = $this->isClientAdmin($client_id, $user);
        return response()->json($res);
    }


    private function getBadges($user) {
        return $user->badges()
            ->where('do_not_possess', false)
            ->where(function($q) {
                $q->whereNotNull('badge_api_id')->orWhere('url', '<>', '');
            })
            ->with('badgeApi')
            ->get();
    }


    private function isClientAdmin($client_id, $user) {
        $res = $user->clients()->where('client_id', $client_id)->first();
        return $res && $res->pivot->admin;
    }

}
