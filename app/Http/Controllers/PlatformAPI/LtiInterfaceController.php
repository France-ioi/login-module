<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\LoginModule\LTI\LTIHelper;
use App\LoginModule\LTI\LTITokenGenerator;

class LtiInterfaceController extends PlatformAPIController
{

    protected $lti;

    public function __construct(LTIHelper $lti, LTITokenGenerator $tokenizer) {
        $this->lti = $lti;
        $this->tokenizer = $tokenizer;
    }


    public function entry(Request $request) {
        $_POST = $request->get('post_params');
        $origin = [
            'http_method' => $request->get('http_method'),
            'http_url' => $request->get('http_url')
        ];
        $conn = $this->lti->handleRequest($origin);
        $token = $this->tokenizer->generateToken($conn);
        return $this->makeResponse($token, $request->get('client')->secret);
    }


    private function validatorSendResult(array $data) {
        return Validator::make($data, [
            'lti_connection_id' => 'required',
            'score' => 'required'
        ]);
    }


    public function sendResult(Request $request) {
        $validator = $this->validatorSendResult($request->all());
        if($validator->fails()) {
            $res = [
                'success' => false,
                'error' => 'Wrong params'
            ];
            return $this->makeResponse($res, $request->get('client')->secret);
        }
        $res = $this->lti->sendResult(
            $request->get('lti_connection_id'),
            $request->get('score')
        );
        if(!$res) {
            $this->lti->scheduleSendResult(
                $request->get('lti_connection_id'),
                $request->get('score')
            );
        }
        return $this->makeResponse($res, $request->get('client')->secret);
    }
}
