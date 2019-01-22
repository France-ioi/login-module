<?php

namespace App\Http\Controllers\Verification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Profile\Verification\Verification;
use App\LoginModule\Platform\PlatformContext;


class IndexController extends Controller
{

    protected $verification;

    public function __construct(Verification $verification) {
        $this->verification = $verification;
    }


    public function index(Request $request, PlatformContext $context) {
        $methods = $this->verification->methods();
        if(count($methods) == 0) {
            return redirect('/profile');
        }
        $client = $context->client();
        return view('verification.index', [
            'unverified_attributes' => $this->verification->unverifiedAttributes($request->user()),
            'platform_name' => $client ? $client->name : trans('app.name'),
            'verifications' => $this->verification->verifications($request->user()),
            'methods' => $methods,
            'continue_url' => $context->continueURL()
        ]);
    }



    public function delete($id, Request $request) {
        $verification = $request->user()->verifications()->findOrFail($id);
        if(!is_null($verification->file)) {
            \Storage::delete('/verifications/'.$verification->file);
        }
        $verification->delete();
        return redirect('/verification');
    }

}
