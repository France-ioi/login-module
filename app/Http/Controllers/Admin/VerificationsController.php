<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Verification;
use App\VerificationMethod;

class VerificationsController extends Controller
{

    public function edit() {
        $methods = VerificationMethod::whereIn('name', ['id_upload', 'classroom_upload'])->get()->pluck('id');
        $verifications = Verification::whereIn('method_id', $methods)->
            where('status', 'pending')->
            paginate(1);

        $verification = $verifications->first();
        if(!$verification) {
            return view('admin.verifications.edit_empty');
        }
        return view('admin.verifications.edit', [
            'verifications' => $verifications,
            'verification' => $verification
        ]);
    }


    public function update($id, Request $request) {
        $this->validate($request, [
            'status' => 'required|in:approved,rejected',
            'user_attributes' => 'required_if:status,approved|array',
            'confidence' => 'nullable|integer|min:0|max:100'
        ]);
        $verification = Verification::findOrFail($id);

        if($request->get('status') == 'approved') {
            $approved_attributes = array_intersect(
                $verification->method->user_attributes,
                $request->get('user_attributes'
            ));
            if(!count($approved_attributes)) {
                return redirect()->back()->withErrors([
                    'user_attributes' => 'Choose one at least'
                ]);
            }
            $verification->rejected_attributes = array_diff(
                $verification->user_attributes,
                $approved_attributes
            );
            $verification->user_attributes = $approved_attributes;
        }
        $verification->message = $request->get('message');
        $verification->status = $request->get('status');
        $verification->confidence = $request->get('confidence');
        if(!is_null($verification->file)) {
            \Storage::delete('/verifications/'.$verification->file);
            $verification->file = null;
        }
        $verification->save();

        if($request->has('page_url')) {
            return redirect($request->get('page_url'));
        }
        return redirect('/admin/verifications/edit');
    }
}
