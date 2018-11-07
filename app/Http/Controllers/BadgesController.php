<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BadgeApi;
use App\Badge;
use App\LoginModule\Platform\BadgeRequest;
use App\LoginModule\Platform\PlatformContext;

class BadgesController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function index(Request $request) {
        return view('badges.index', [
            'available' => $this->getAvailableBadges($request->user())
        ]);
    }


    private function getAvailableBadges($user) {
        $attached = $user->badges()
            ->whereNotNull('badge_api_id')
            ->where('do_not_possess', false)
            ->pluck('badge_api_id');
        $q = BadgeApi::whereNotIn('id', $attached);
        $client = $this->context->client();
        if($client && $client->badge_api_id) {
            $q->orderByRaw('IF(id='.$client->badge_api_id.',0,1)');
        }
        return $q->get()->pluck('name', 'id');
    }


    public function add(Request $request) {
        $this->validate($request, [
            'code' => 'required'
        ]);
        $badge_api = BadgeApi::findOrFail($request->get('badge_api_id'));
        $code = $request->get('code');
        if($this->badgeExists($code, $badge_api->id)) {
            return redirect()->back()->withErrors([
                'code' => trans('badges.errors.code_used')
            ]);
        }
        if(false === BadgeRequest::verify($badge_api->url, $request->get('code'))) {
            return redirect()->back()->withErrors([
                'code' => trans('badge.errors.code_invalid')
            ]);
        }

        $badge = new Badge([
            'badge_api_id' => $badge_api->id,
            'code' => $code,
            'url' => '',
            'do_not_posses' => false
        ]);
        $request->user()->badges()->save($badge);
        // attach
        return redirect()->back()->with([
            'status' => trans('badges.success', ['name' => $badge_api->name])
        ]);
    }


    private function badgeExists($code, $badge_api_id) {
        return (bool) Badge::where('code', $code)->where('badge_api_id', $badge_api_id)->first();
    }

}
