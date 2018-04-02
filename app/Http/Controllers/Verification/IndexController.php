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
        $client = $context->client();
        return view('verification.index', [
            'unverified_attributes' => $this->verification->unverifiedAttributes($request->user()),
            'platform_name' => $client ? $client->name : trans('app.name'),
            'verifications' => $this->verification->verifications($request->user()),
            'methods' => $this->verification->methods(),
            'continue_url' => $context->continueURL('/account')
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
