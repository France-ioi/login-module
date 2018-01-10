<?php
namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlatformAPIController extends Controller
{


    public function makeResponse($data, $secret) {
        $data = json_encode($data);
        $data = openssl_encrypt($data, 'AES-128-ECB', $secret);
        return response($data);
    }

}