<?php
namespace App\Http\Controllers\ClientAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\ClientAdmin\Controller;
use App\User;
use App\Client;
use App\VerificationMethod;
use App\Verification as Verification;
use App\LoginModule\Platform\PlatformUser;

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
        if($request->get('teacher_not_verified')) {
            $query->where('role', 'teacher');
            $query->whereDoesntHave('verifications', function($query) {
                $query->where('client_id', $this->client->id)
                    ->where('status', 'approved')
                    ->where('user_attributes', 'LIKE', '%"role"%');
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


    public function showVerification($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        
        $method = VerificationMethod::where('name', 'manual')->firstOrFail();
        $admin_verification = $this->getAdminVerification($user, $method);

        return view('client_admin.users.verification', [
            'client' => $this->client,
            'user' => $user,
            'attributes' => $this->getMethodsAttributes($this->client->verification_methods),
            'verification_required' => array_fill_keys($this->client->verifiable_attributes, 1),
            'verified_attributes' => array_fill_keys($this->getUserVerifiedAttributes($user, $method->id), 1),
            'admin_verified' => array_fill_keys($admin_verification->user_attributes, 1),
            'refer_page' => $request->get('refer_page', '/client_admin/'.$this->client->id.'/users')
        ]);        
    }



    public function updateVerification($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);

        $admin_verified = $request->get('admin_verified', []);
        $method = VerificationMethod::where('name', 'manual')->firstOrFail();
        $attributes = $this->getMethodsAttributes($this->client->verification_methods);
        $user_attributes = [];
        foreach($attributes as $attr) {
            if(isset($admin_verified[$attr])) {
                $user_attributes[] = $attr;    
            }
        }
        $admin_verification = $this->getAdminVerification($user, $method);
        if(count($user_attributes)) {
            $admin_verification->client_id = $this->client->id;
            $admin_verification->method_id = $method->id;
            $admin_verification->user_attributes = $user_attributes;
            $admin_verification->status = 'approved';
            $admin_verification->save();
        } else {
            $admin_verification->delete();            
        }
        return redirect($request->get('refer_page'))->with(['status' => 'Admin verification udpated']);
    }    


    // ban form

    public function showBan($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        $link = PlatformUser::link($client_id, $user_id);
        return view('client_admin.users.ban', [
            'client' => $this->client,
            'user' => $user,
            'banned' => $link->banned,
            'refer_page' => $request->get('refer_page', '/client_admin/'.$this->client->id.'/users')
        ]);
    }


    public function updateBan($client_id, $user_id, Request $request) {
        $user = $this->getUser($user_id);
        PlatformUser::setBanned($this->client->id, $user->id, $request->get('banned') ? 1 : 0);
        return redirect($request->get('refer_page'))->with(['status' => 'Ban status updated']);
    }    



    public function export(Request $request) {
        $callback = function() {
            $fh = fopen('php://output', 'w');
            $columns = [
                'ID', 
                'Created at', 
                'First name', 
                'Last name',
                'Primary email',
                'Secondary email',
                'Role',
                'Subscription',
                'Last activity',
                'Admin',
                'Banned',
            ];
            fputcsv($fh, $columns);

            User::with('emails', 'clients')
                ->whereHas('clients', function($query) {
                    $query->where('client_id', $this->client_id);
                })->chunk(100, function($users) use ($fh) {
                    foreach($users as $user) {
                        $row = [
                            $user->id,
                            $user->created_at,
                            $user->first_name,
                            $user->last_name,
                            $user->primary_email,
                            $user->secondary_email,
                            $user->role,
                            $user->subscription_news ? 'yes' : 'no',
                            $user->clients[0]->pivot->last_activity,
                            $user->clients[0]->pivot->admin ? 'yes' : 'no',
                            $user->clients[0]->pivot->banned ? 'yes' : 'no'
                        ];
                        fputcsv($fh, $row);
                    }
                });
            fclose($fh);
        };
        $file = preg_replace('/[^a-z0-9]+/', '_', strtolower($this->client->name)).date('Y-m-d_H:i:s').'.csv';
        return $this->outputFile($file, $callback);
    }



    // misc
    private function getUser($user_id) {
        return User::where('id', $user_id)->whereHas('clients', function($q) {
            $q->where('client_id', $this->client_id);
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
                $q->orWhere('client_id', $this->client->id);
            })->get();
        $res = [];
        foreach($verifications as $verification) {
            $res += $verification->user_attributes;
        }
        return array_unique($res);        
    }


    private function getAdminVerification($user, $method) {
        return $user->verifications()
            ->where('client_id', $this->client->id)
            ->where('method_id', $method->id)
            ->firstOrNew();
    }


    private function outputFile($file_name, $callback) {
        $headers = array(
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$file_name,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        );
        return response()->stream($callback, 200, $headers);
    }    

}
