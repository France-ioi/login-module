<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class IdUploadController extends Controller
{

    const SESSION_KEY = 'verification.id_upload.code';


    public function index(Request $request) {
        if(!($code = $request->session()->get(self::SESSION_KEY))) {
            $code = mt_rand(10000, 99999).mt_rand(10000, 99999);
            $request->session()->put(self::SESSION_KEY, $code);
        }


        $method = VerificationMethod::where('name', 'id_upload')->firstOrFail();
        return view('verification.methods.id_upload', [
            'code' => $code,
            'method' => $method,
            'max_file_size' => config('ui.profile_picture.max_file_size')
        ]);
    }


    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'required|image',
            'user_attributes' => 'required|array'
        ]);

        $code = $request->session()->pull(self::SESSION_KEY);
        if(!$code) {
            return redirect()->back()->withErrors([
                'code' => 'Code expired'
            ]);
        }

        $method = VerificationMethod::where('name', 'id_upload')->firstOrFail();
        $user_attributes = array_intersect($method->user_attributes, $request->get('user_attributes'));
        if(!count($user_attributes)) {
            return redirect()->back()->withErrors([
                'user_attributes' => 'Choose one at least'
            ]);
        }

        $filename = str_random(40).'.'.$request->file('file')->extension();
        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => $user_attributes,
            'status' => 'pending',
            'file' => $filename,
            'code' => $code
        ]);
        $request->user()->verifications()->save($verification);

        $request->file('file')->storeAs('verifications', $filename);

        return redirect('/verification');
    }

}
