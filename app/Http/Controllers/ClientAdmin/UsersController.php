<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\ClientAdmin\Controller;
use App\User;
use App\Client;
use App\VerificationMethod;
use App\Verification as Verification;
use App\LoginModule\Platform\PlatformUser;
use App\LoginModule\Profile\SchemaBuilder;
use App\LoginModule\Profile\UserProfile;
use Illuminate\Support\Facades\DB;
use App\Helpers\SortableTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller {


    private $sort_fields = [
        'id' => 'users.id',
        'created_at' => 'users.created_at',
        'last_activity' => 'oauth_client_user.last_activity',
        'login' => 'users.login',
        'emails' => 'emails',
        'name' => 'name',
    ];    

    public function index(Request $request) {
        $q = $this->getUsersQuery($request);
        SortableTable::orderBy($q, $this->sort_fields);
        return view('client_admin.users.index', [
            'client' => $this->context->client(),
            'rows' => $q->paginate()->appends($request->all()),
            'refer_page' => $request->fullUrl()
        ]);
    }


    private function getUsersQuery($request) {
        $q = DB::table('users')
            ->select(DB::raw('
                users.*, 
                CONCAT_WS(" ", users.first_name, users.last_name) as name,
                oauth_client_user.last_activity,
                (SELECT GROUP_CONCAT(emails.email SEPARATOR "\\n") FROM emails WHERE emails.user_id = users.id) as emails
            '))
            ->join('oauth_client_user', 'oauth_client_user.user_id', '=', 'users.id')
            ->where('oauth_client_user.client_id', $this->context->client()->id);

        if($request->get('id')) {
            $q->where('users.id', $request->get('id'));
        }
        if($request->get('login')) {
            $q->where('users.login', 'LIKE', '%'.$request->get('login').'%');
        }
        if($request->get('first_name')) {
            $q->where('users.first_name', 'LIKE','%'.$request->get('first_name').'%');
        }
        if($request->get('last_name')) {
            $q->where('users.last_name', 'LIKE', '%'.$request->get('last_name').'%');
        }
        if($request->get('email')) {
            $q->whereExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                    ->from('emails')
                    ->whereRaw('emails.user_id = users.id')
                    ->where('emails.email', 'LIKE', '%'.$request->get('email').'%');
            });
        }
        if($request->get('teacher_not_verified')) {
            $q->where('users.role', 'teacher');
            $q->whereNotExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                    ->from('verifications')
                    ->whereRaw('verifications.user_id = users.id')
                    ->where('verifications.client_id', $this->context->client()->id)
                    ->where('verifications.status', 'approved')
                    ->where('verifications.user_attributes', 'LIKE', '%"role"%');
            });
        }            

        return $q;
    }



    public function showVerification($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        
        $method = VerificationMethod::where('name', 'manual')->firstOrFail();
        $admin_verification = $this->getAdminVerification($user, $method);

        return view('client_admin.users.verification', [
            'client' => $this->context->client(),
            'user' => $user,
            'attributes' => $this->getMethodsAttributes($this->context->client()->verification_methods),
            'verification_required' => array_fill_keys($this->context->client()->verifiable_attributes, 1),
            'verified_attributes' => array_fill_keys($this->getUserVerifiedAttributes($user, $method->id), 1),
            'admin_verified' => array_fill_keys($admin_verification->user_attributes, 1),
            'refer_page' => $this->getReferPage($request)
        ]);        
    }



    public function updateVerification($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);

        $admin_verified = $request->get('admin_verified', []);
        $method = VerificationMethod::where('name', 'manual')->firstOrFail();
        $attributes = $this->getMethodsAttributes($this->context->client()->verification_methods);
        $user_attributes = [];
        foreach($attributes as $attr) {
            if(isset($admin_verified[$attr])) {
                $user_attributes[] = $attr;    
            }
        }
        $admin_verification = $this->getAdminVerification($user, $method);
        if(count($user_attributes)) {
            $admin_verification->client_id = $this->context->client()->id;
            $admin_verification->method_id = $method->id;
            $admin_verification->user_attributes = $user_attributes;
            $admin_verification->status = 'approved';
            $admin_verification->save();
        } else {
            $admin_verification->delete();            
        }
        return redirect($this->getReferPage($request))->with(['status' => 'Admin verification udpated']);
    }    


    // ban form

    public function showBan($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $link = PlatformUser::link($client_id, $user_id);
        return view('client_admin.users.ban', [
            'client' => $this->context->client(),
            'user' => $user,
            'banned' => $link->banned,
            'refer_page' => $this->getReferPage($request)
        ]);
    }


    public function updateBan($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        PlatformUser::setBanned(
            $this->context->client()->id,
            $user->id, 
            $request->get('banned') ? 1 : 0
        );
        $request->session()->flash('status', 'Ban status updated');
        $url = $this->context->adminIntarface()->userLogout(
            $user_id,
            $this->getReferPage($request)
        );
        return redirect($url);
    }    



    // edit user
    public function edit($client_id, $user_id, Request $request, SchemaBuilder $schema_builder) {
        $user = $this->getUser($user_id);
        $schema = $schema_builder->build($user, [], []);
        return view('client_admin.users.edit', [
            'client' => $this->context->client(),
            'user' => $user,
            'form' => [
                'model' => $user,
                'url' => '/client_admin/'.$this->context->client()->id.'/users/'.$user->id.'/edit',
                'method' => 'post',
                'files' => true,
                'id' => 'profile'
            ],
            'schema' => $schema,
            'official_domains' => $this->context->client()->official_domains->pluck('domain'),
            'refer_page' => $this->getReferPage($request)            
        ]);
    }


    public function update($client_id, $user_id, Request $request, SchemaBuilder $schema_builder, UserProfile $profile) {
        $user = $this->getUser($user_id);
        $schema = $schema_builder->build($user, [], []);
        $this->validate($request, $schema->rules());
        // $this->clearVerifications($user, $request); ??

        $result = $profile->update(
            $user,
            $request, 
            $schema->fillableAttributes()
        );
        if($result !== true) {
            return redirect()->back()->withInput()->withErrors($result);
        }
        $url = $this->context->adminIntarface()->userRefresh(
            $user_id,
            $this->getReferPage($request)
        );        
        return redirect($url);
    }    


    // password reset
    public function showPassword($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        return view('client_admin.users.password', [
            'client' => $this->context->client(),
            'user' => $user,
            'refer_page' => $this->getReferPage($request)            
        ]);
    }

    public function updatePassword($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $user->password = \Hash::make($request->get('password'));
        $user->save();
        return redirect($this->getReferPage($request))->with(['status' => 'Password changed']);
    }    

    // login
    public function login($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $user_clients = $user->clients->pluck('id');
        $admin_clients = $request->user()->clients->pluck('id');
        //dd($user_clients, $admin_clients);
        $diff = count($user_clients->diff($admin_clients));
        if($diff) {
            return redirect($this->getReferPage($request))->withError('You must be admins of all the platforms that the user has visited');
        }
        Auth::logout();
        Session::flush();
        Auth::login($user);
        $url = $this->context->adminIntarface()->userLogin($user_id);        
        return redirect($url);        
    }


    // misc
    private function getUser($user_id) {
        return User::where('id', $user_id)->whereHas('clients', function($q) {
            $q->where('client_id', $this->context->client()->id);
        })->firstOrFail();
    }    


    private function getMethodsAttributes($methods) {
        $res = [];
        foreach($methods as $method) {
            $res = array_merge($res, $method->user_attributes);
        }
        return array_unique($res);
    }


    private function getUserVerifiedAttributes($user, $exclude_method_id) {
        $verifications = $user->verifications()
            ->where('status', 'approved')
            ->where('method_id', '<>', $exclude_method_id)
            ->where(function($q) {
                $q->whereNull('client_id');
                $q->orWhere('client_id', $this->context->client()->id);
            })->get();
        $res = [];
        foreach($verifications as $verification) {
            $res = array_merge($res, $verification->user_attributes);
        }
        return array_unique($res);        
    }


    private function getAdminVerification($user, $method) {
        return $user->verifications()
            ->where('client_id', $this->context->client()->id)
            ->where('method_id', $method->id)
            ->firstOrNew();
    }


    private function getReferPage($request) {
        return $request->get('refer_page', '/client_admin/'.$this->context->client()->id.'/users');
    }

}
