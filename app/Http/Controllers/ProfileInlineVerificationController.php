<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;
use App\Verification;
use App\VerificationMethod;


class ProfileInlineVerificationController extends Controller
{
    
    
    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function sendCode(Request $request) {
        $client = $this->context->client();
        if(!$client) {
            return response()->json([
                'error' => 'Client context required.'
            ]);            
        }
        
        $email = $request->get('email');
        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();
        $user = $request->user();

        $verification = new Verification([
            'client_id' => $client->id,
            'method_id' => $method->id,
            'user_attributes' => $method->user_attributes,
            'status' => 'pending',
            'email' => $email
        ]);
        $user->verifications()->save($verification);

        $verification->sendVerificationCode();

        $res = [
            'success' => true,
            'email' => $email
        ];
        return response()->json($res);
    }


    public function verifyCode(Request $request) {
        $client = $this->context->client();
        if(!$client) {
            return response()->json([
                'error' => 'Client context required.'
            ]);            
        }        

        $code = $request->get('code');
        $email = $request->get('email');
        $method = VerificationMethod::where('name', 'email_domain')->firstOrFail();
        $user = $request->user();        

        $verification = $user->verifications()
            ->where('method_id', $method->id)
            ->where('client_id', $client->id)
            ->where('email', $email)
            ->first();

        if($verification && $verification->code == $code) {
            $verification->status = 'approved';
            $verification->save();
            
            $res = [
                'success' => true,
                'email' => $email
            ];            
        } else {
            $res = [
                'error' => 'Wrong code or verification record not found.'
            ];            
        }
        return response()->json($res);        
    }
}
