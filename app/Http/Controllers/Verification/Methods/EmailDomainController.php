<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;
use App\OfficialDomain;
use App\Email;
use App\LoginModule\TeacherDomain;
use App\LoginModule\Profile\Verification\Verification as ProfileVerification;


class EmailDomainController extends Controller
{


    public function index(Request $request) {
        if(!$request->user()->country_code) {
            return view('verification.methods.email_domain_alert', [
                'alert' => 'user_country_empty'
            ]);
        }
        $domains = OfficialDomain::where('country_code', $request->user()->country_code)->get();
        if(!count($domains)) {
            return view('verification.methods.email_domain_alert', [
                'alert' => 'no_country_domains'
            ]);
        }
        $domains_options = ['' => '...'];
        foreach($domains as $domain) {
            $domains_options[$domain->domain] = '@'.$domain->domain;
        }
        return view('verification.methods.email_domain', [
            'roles' => [
                'primary' => trans('profile.primary_email'),
                'secondary' => trans('profile.secondary_email')
            ],
            'domains' => $domains_options
        ]);
    }


    public function store(Request $request) {
        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();

        if(!TeacherDomain::verifyDomain($request->get('domain'), $request->user()->country_code)) {
            return redirect()->back()->withErrors([
                'domain' => 'Wrong value'
            ]);
        }
        $this->validate($request, [
            'account' => 'required'
        ]);

        $v = $request->get('account').'@'.$request->get('domain');
        if($email = $request->user()->emails()->where('role', $request->get('role'))->first()) {
            $email->email = $v;
            $email->save();
        } else {
            $email = new Email([
                'email' => $v,
                'role' => $request->get('role')
            ]);
            $request->user()->emails()->save($email);
        }
        $email->requireVerification();

        ProfileVerification::clear(
            $request->user(),
            $request->get('role').'_email'
        );

        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => ['role'],
            'status' => 'approved'
        ]);
        $request->user()->verifications()->save($verification);

        return redirect('/verification');
    }
}
