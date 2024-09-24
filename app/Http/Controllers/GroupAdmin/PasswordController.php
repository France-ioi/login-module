<?php

namespace App\Http\Controllers\GroupAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;
use App\User;
use App\Email;
use App\LoginModule\Badges;
use App\LoginModule\Platform\BadgeRequest;
use Illuminate\Support\Facades\Password;
use Mail;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PasswordController extends Controller
{

    public function index(Request $request) {
        if($request->get('token')) {
            $query = [
                'client_id' => $request->get('client_id'),
                'token' => $request->get('token'),
                'return_url' => $request->get('return_url'),
                'alg' => $request->get('alg')
            ];
        } else {
            $query = $request->session()->get('group_admin_query');
        }

        if($request->get('token')) {
            $request->session()->put('group_admin_query', $query);
            return redirect()->route('group_admin.password');
        }

        $user = $this->checkQueryParameters($request, $query);
        if($user instanceof \Illuminate\Http\Response) {
            return $user;
        }

        return view('group_admin.index', [
            'user' => $user,
            'return_url' => $query['return_url'],
            'test' => ""
        ]);
    }

    public function modifyPassword(Request $request) {
        $query = $request->session()->get('group_admin_query');
        $user = $this->checkQueryParameters($request, $query);
        if($user instanceof \Illuminate\Http\Response) {
            return $user;
        }

        $this->validate($request, [
            'password' => 'required|confirmed|min:6'
        ]);

        $user->update([
            'password' => \Hash::make($request->input('password'))
        ]);

        return redirect()->route('group_admin.password')->with('status', 'Mot de passe mis à jour.');
    }

    private function checkQueryParameters(Request $request, $query) {
        $client = Client::find($query['client_id']);
        if(!$client) {
            return response('Invalid client_id', 404);
        }

        try {
            if($query['alg'] == 'AES-256-GCM') {
                $cipher = hex2bin($query['token']);
                $iv = substr($cipher, 0, 12);
                $cipherText = substr($cipher, 12, -16);
                $tag = substr($cipher, -16);
                $token = json_decode(openssl_decrypt($cipherText, 'aes-256-gcm', substr($client->secret, 0, 32), OPENSSL_RAW_DATA, $iv, $tag));
            } else {
                $token = json_decode(openssl_decrypt($query['token'], 'AES-128-ECB', $client->secret));
            }
        } catch (\Exception $e) {
            return response('Invalid token', 400);
        }
        if(!$token) {
            return response('Invalid token', 400);
        }

        if(!isset($token->requester_id) || $token->requester_id != $request->user()->id) {
            return response('Invalid token (invalid requesting user)', 400);
        }

        if(!isset($token->exp) || $token->exp < time()) {
            return response('Invalid token (expired)', 400);
        }

        $user = User::query()->where('id', '=', $token->target_id)->first();
        if(!$user) {
            return response('User not found', 404);
        }

        return $user;
    }
}