<?php
/// legacy code


namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;
use App\LoginModule\Platform\PlatformContext;

class EmailDomainController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }        


    public function index(Request $request) {
        $client = $this->context->client();
        if(!$client) {
            abort(403);
        }
        $official_domains = $client->official_domains->pluck('domain', 'domain')->toArray();

        $user = $request->user();

        $official_email = '';
        if($this->testEmail($user->primary_email, $official_domains)) {
            $official_email = $user->primary_email;
        } else if($this->testEmail($user->secondary_email, $official_domains)) {
            $official_email = $user->secondary_email;
        }


        list($account, $domain) = explode('@', $official_email);

        return view('verification.methods.email_domain_step1', [
            'account' => $account,
            'domain' => $domain,
            'official_domains' => $official_domains
        ]);        
    }


    private function testEmail($email, $domains) {
        $tmp = explode('@', $email);
        return count($tmp) == 2 && isset($domains[$tmp[1]]);
    }


    public function sendCode(Request $request) {
        $client = $this->context->client();
        if(!$client) {
            abort(403);
        }

        $this->validate($request, [
            'account' => 'required',
            'domain' => 'required'
        ]);
        $account = $request->get('account');
        $domain = $request->get('domain');
        
        $official_domains = $client->official_domains->pluck('domain', 'domain')->toArray();
        if(!isset($official_domains[$domain])) {
            return redirect()->back()->withErrors([
                'domain' => 'Wrong value'
            ]);
        }

        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();
        $user = $request->user();

        $user->verifications()
            ->where('client_id', $client->id)
            ->where('method_id', $method->id)
            ->where('status', 'pending')
            ->delete();

        $verification = new Verification([
            'client_id' => $client->id,
            'method_id' => $method->id,
            'user_attributes' => $method->user_attributes,
            'status' => 'pending',
            'email' => $account.'@'.$domain
        ]);
        $user->verifications()->save($verification);

        $verification->sendVerificationCode();

        return redirect('/verification/email_domain/input_code/'.$verification->id)->with([
            'email' => $account.'@'.$domain
        ]);
    }


    public function showInputCode($id, Request $request) {
        $client = $this->context->client();
        if(!$client) {
            abort(403);
        }
        $user = $request->user();        
        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();
        
        $verification = Verification::where('id', $id)
            ->where('user_id', $user->id)
            ->where('client_id', $client->id)
            ->where('status', 'pending')
            ->where('method_id', $method->id)
            ->firstOrFail();
        
        return view('verification.methods.email_domain_step2', [
            'verification' => $verification
        ]);        
    }


    public function validateCode($id, Request $request) {
        $client = $this->context->client();
        if(!$client) {
            abort(403);
        }
        $user = $request->user();        
        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();
        
        $verification = Verification::where('id', $id)
            ->where('user_id', $user->id)
            ->where('method_id', $method->id)
            ->where('client_id', $client->id)
            ->where('code', $request->get('code'))
            ->first();
        
            if($verification) {
            $verification->status = 'approved';
            $verification->save();
            return redirect('/verification');
        } 
        return redirect()->back()->withErrors([
            'code' => 'Wrong code'
        ]);
    }


    /*
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
        $email->sendVerificationCode();

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
    */
}
