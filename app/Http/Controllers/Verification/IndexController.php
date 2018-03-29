<?php

namespace App\Http\Controllers\Verification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Profile\Verification\Verification;


class IndexController extends Controller
{

    protected $verification;

    public function __construct(Verification $verification) {
        $this->verification = $verification;
    }


    public function index(Request $request) {
        return view('verification.index', [
            'unverified_attributes' => $this->verification->unverifiedAttributes($request->user()),
            'verifications' => $this->verification->verifications($request->user()),
            'methods' => $this->verification->methods()
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
