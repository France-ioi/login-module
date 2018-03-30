<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class ClassroomUploadController extends Controller
{

    const SESSION_KEY = 'verification.classroom_upload.code';


    public function index(Request $request) {
        if(!($code = $request->session()->get(self::SESSION_KEY))) {
            $code = mt_rand(10000, 99999).mt_rand(10000, 99999);
            $request->session()->put(self::SESSION_KEY, $code);
        }

        return view('verification.methods.classroom_upload', [
            'code' => $code,
            'max_file_size' => config('ui.profile_picture.max_file_size')
        ]);
    }


    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'required|image'
        ]);

        $code = $request->session()->pull(self::SESSION_KEY);
        if(!$code) {
            return redirect()->back()->withErrors([
                'code' => 'Code expired'
            ]);
        }

        $method = VerificationMethod::where('name', 'classroom_upload')->firstOrFail();

        $filename = str_random(40).'.'.$request->file('file')->extension();
        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => $method->user_attributes,
            'status' => 'pending',
            'file' => $filename,
            'code' => $code
        ]);
        $request->user()->verifications()->save($verification);

        $request->file('file')->storeAs('verifications', $filename);

        return redirect('/verification');
    }
}
