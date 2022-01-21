<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OfficialDomain;
use App\LoginModule\Platform\PlatformContext;
class OfficialDomainsController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }    


    public function index(Request $request) {
        $res = [];
        $client = $this->context->client();
        if($client) {
            $res = $client->official_domains->pluck('domain');
        }
        return response()->json($res);
    }

}
