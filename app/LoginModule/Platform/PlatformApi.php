<?php

namespace App\LoginModule\Platform;

class PlatformApi {

    protected $http_client;
    protected $url;

    public function __construct($client) {
        $this->url = $client ? $client->api_url : null;
        if($this->url) {
            $this->http_client = new \GuzzleHttp\Client();
        }
    }


    public function verify($code) {
        if(!$this->url) {
            return false;
        }
        $data = [
            'form_params' => [
                'module' => 'group_code',
                'action' => 'verify',
                'code' => $code
            ]
        ];
        $res = $this->http_client->request('POST', $this->url, $data);
        if($res->getStatusCode() == 200) {
            $data = json_decode($res->getBody(), true);
            return isset($data['success']) && $data['success'];
        }
        return false;
    }

}
