<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\LoginModule\LTI\LTIHelper;


class LtiRequestController extends PlatformAPIController
{

    protected $lti;

    public function __construct(LTIHelper $lti) {
        $this->lti = $lti;
    }


    private function validatorSendResult(array $data) {
        return Validator::make($data, [
            'user_id' => 'required',
            'content_id' => 'required',
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
        $res = $this->lti->sendResultByContent(
            $request->get('user_id'),
            $request->get('content_id'),
            $request->get('score')
        );
        return $this->makeResponse($res, $request->get('client')->secret);
    }
}
