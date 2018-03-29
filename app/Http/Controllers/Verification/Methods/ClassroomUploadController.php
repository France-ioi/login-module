<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class ClassroomUploadController extends Controller
{
    public function index(Request $request) {
        return view('verification.methods.classroom_upload', [
            'max_file_size' => config('ui.profile_picture.max_file_size')
        ]);
    }


    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'required|image'
        ]);

        $method = VerificationMethod::where('name', 'classroom_upload')->firstOrFail();

        $filename = str_random(40).'.'.$request->file('file')->extension();
        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => $method->user_attributes,
            'status' => 'pending',
            'file' => $filename
        ]);
        $request->user()->verifications()->save($verification);

        $request->file('file')->storeAs('verifications', $filename);

        return redirect('/verification');
    }
}
