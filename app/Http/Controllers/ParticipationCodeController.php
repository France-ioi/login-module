<?php

namespace App\Http\Controllers;

use App\LoginModule\Platform\PlatformContext;
use Illuminate\Http\Request;

class ParticipationCodeController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function index(Request $request) {
        $url =  $this->context->continueUrl('/account');

        $client = $this->context->client();
        if($client) {
            $platform_group = $request->user()->platformGroups()->where('client_id', $client->id)->first();
            if($platform_group) {
                return view('participation_code.index', [
                    'participation_code' => $platform_group->participation_code,
                    'url' => $url
                ]);
            }
        }

        return redirect($url);
    }

}
