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
        return view('verification.methods.email_domain_step1', [
            'email' => $this->getEmailDetails($request, $official_domains),
            'official_domains' => $official_domains
        ]);        
    }



    private function getEmailDetails($request, $official_domains) {
        $email = $request->get('email');
        if($res = $this->testOfficialEmail($email, $official_domains)) {
            return $res;
        }
        $user = $request->user();
        if($res = $this->testOfficialEmail($user->primary_email, $official_domains)) {
            return $res;
        }
        if($res = $this->testOfficialEmail($user->secondary_email, $official_domains)) {
            return $res;
        }            
        return [
            'account' => '',
            'domain' => ''
        ];
    }


    private function testOfficialEmail($email, $domains) {
        if(empty($email)) {
            return false;
        }
        $tmp = explode('@', $email);
        if(count($tmp) == 2 && isset($domains[$tmp[1]])) {
            return [
                'account' => $tmp[0],
                'domain' => $tmp[1]
            ];
        };
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
                'domain' => trans('ui.wrong_value')
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

        if(!$verification->sendVerificationCode()) {
            $verification->delete();
            return redirect()->back()->withErrors([
                'account' => trans('ui.wrong_value')
            ]);
        }
        

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
            'verification' => $verification,
            'code' => $request->get('code')
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
            return redirect('/verification')->with([
                'last_verification_attributes' => $verification->user_attributes
            ]);
        } 
        return redirect()->back()->withErrors([
            'code' => trans('verification.email_domain.wrong_code')
        ]);
    }

}
