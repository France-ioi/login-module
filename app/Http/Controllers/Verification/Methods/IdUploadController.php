<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class IdUploadController extends Controller
{
    public function index(Request $request) {
        $method = VerificationMethod::where('name', 'id_upload')->firstOrFail();
        return view('verification.methods.id_upload', [
            'method' => $method,
            'max_file_size' => config('ui.profile_picture.max_file_size')
        ]);
    }


    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'required|image',
            'user_attributes' => 'required|array'
        ]);

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
            'file' => $filename
        ]);
        $request->user()->verifications()->save($verification);

        $request->file('file')->storeAs('verifications', $filename);

        return redirect('/verification');
    }
}
