<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\ClientAdmin\Controller;
use App\User;
use App\Client;
use App\VerificationMethod;
use App\Verification;

class UsersController extends Controller {


    public function index(Request $request) {
        $query = User::query();
        if($request->get('id')) {
            $query->where('id', $request->get('id'));
        }
        if($request->get('login')) {
            $query->where('login', 'LIKE', '%'.$request->get('login').'%');
        }
        if($request->get('first_name')) {
            $query->where('first_name', 'LIKE','%'.$request->get('first_name').'%');
        }
        if($request->get('last_name')) {
            $query->where('last_name', 'LIKE', '%'.$request->get('last_name').'%');
        }
        if($request->get('email')) {
            $query->whereHas('emails', function($query) use ($request) {
                $query->where('email', 'LIKE', '%'.$request->get('email').'%');
            });
        }
        $query->whereHas('clients', function($query) {
            $query->where('client_id', $this->client_id);
        });

        return view('client_admin.users.index', [
            'client' => $this->client,
            'users' => $query->paginate(),
            'refer_page' => $request->fullUrl()
        ]);
    }


    public function show($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        return view('client_admin.users.show', [
            'client' => $this->client,
            'user' => $user,
            'refer_page' => $request->get('refer_page', '/client_admin/'.$this->client->id.'/users')
        ]);        
    }


    public function verify($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);

        $verified_attributes = $request->get('verified_attributes', []);
        $method = VerificationMethod::where('name', 'manual')->firstOrFail();
        $user_attributes = [];
        foreach($method->user_attributes as $attr) {
            if(isset($verified_attributes[$attr])) {
                $user_attributes[] = $attr;    
            }
        }

        $res = redirect($request->get('refer_page'));
        if(count($user_attributes)) {
            Verification::create([
                'client_id' => $this->client->id,
                'user_id' => $user->id,
                'method_id' => $method->id,
                'user_attributes' => $user_attributes,
                'status' => 'approved'
            ]);
            $res->with(['status' => 'User attributes verified']);
        }
        return $res;
    }    



    private function getUser($user_id) {
        return User::where('id', $user_id)->whereHas('clients', function($q) {
            $q->where('client_id', $this->client_id);
        })->firstOrFail();
    }
}