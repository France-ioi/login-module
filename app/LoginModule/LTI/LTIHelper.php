<?php
namespace App\LoginModule\LTI;

use App\LoginModule\LTI\Tool\LTI_Tool_Provider;
use App\LoginModule\LTI\Tool\LTI_Tool_Consumer;
use App\LoginModule\LTI\Tool\LTI_Resource_Link;
use App\LoginModule\LTI\Tool\LTI_Outcome;
use App\LoginModule\LTI\Tool\LTI_User;
use App\LtiConnection;
use App\User;
use App\Email;
use App\LtiConfig;
use App\LtiResult;
use App\LoginModule\LoginGenerator;


/*

http://login-module.test/lti/entry?redirectUrl=http%3A%2F%2Falgorea-platform.test%2Fcontents%2F4026%2F4020
*/


class LTIHelper {


    public function __construct(LTIPDO $pdo) {
        $this->db = $pdo->db();
        $this->data_connector = $pdo->connector();
    }


    public function handleRequest($origin = null) {
        $tool = new LTI_Tool_Provider(function() {}, $this->data_connector, $origin);
        $tool->handle_request();
        return $this->syncConnection($tool->user);
    }



    private function syncConnection($lti_user) {
        $lti_user_id = $lti_user->getId();
        $lti_context_id = $lti_user->getResourceLink()->lti_resource_link_id;
        $lti_consumer_key = $lti_user->getResourceLink()->getConsumer()->getKey();

        $lc = LtiConnection::where('lti_consumer_key', $lti_consumer_key)->where('lti_user_id', $lti_user_id)->first();
        if($lc) {
            $user = $lc->user;
        } else {
            $user = $this->createUser($lti_user);
        }

        $conn = LtiConnection::where('lti_consumer_key', $lti_consumer_key)
            ->where('lti_context_id', $lti_context_id)
            ->where('lti_user_id', $lti_user_id)
            ->with('user')
            ->first();
        if(!$conn) {
            $conn = new \App\LtiConnection([
                'lti_consumer_key' => $lti_consumer_key,
                'lti_context_id' => $lti_context_id,
                'lti_user_id' => $lti_user_id
            ]);
            $conn->user()->associate($user);
            $conn->save();
        }

        return $conn;
    }


    private function createUser($lti_user) {
        $prefix = $this->getLoginPrefix($lti_user);
        $login = LoginGenerator::genLogin(
            $lti_user->firstname,
            $lti_user->lastname,
            $prefix
        );
        $user = User::create([
            'first_name' => $lti_user->firstname,
            'last_name' => $lti_user->lastname,
            'login' => $login
        ]);
        $email = new Email([
            'role' => 'primary',
            'email' => $lti_user->email
        ]);
        $user->emails()->save($email);
        return $user;
    }


    private function getLoginPrefix($lti_user) {
        $lti_consumer_key = $lti_user->getResourceLink()->getConsumer()->getKey();
        $conf = LtiConfig::where('lti_consumer_key', $lti_consumer_key)->first();
        if($conf) {
            return $conf->prefix;
        }
        return config('lti.default_login_prefix');
    }


    public function sendResult($lti_connection_id, $score) {
        $lc = LtiConnection::find($lti_connection_id);
        if(!$lc) {
            return false;
        }
        $consumer = new LTI_Tool_Consumer($lc['lti_consumer_key'], $this->data_connector);
        $resourceLink = new LTI_Resource_Link($consumer, $lc['lti_context_id']);
        $outcome = new LTI_Outcome();
        $outcome->setValue($score);
        $user = new LTI_User($resourceLink, $lc['lti_user_id']);
        $res = $resourceLink->doOutcomesService(LTI_Resource_Link::EXT_WRITE, $outcome, $user);
        return $res;
    }


    public function scheduleSendResult($lti_connection_id, $score) {
        LtiResult::create([
            'lti_connection_id' => $lti_connection_id,
            'score' => $score
        ]);
    }


}