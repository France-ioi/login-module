<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UsernameChange;
use App\LoginModule\LoginSuggestion;
use App\LoginModule\Profile\SchemaConfig;

class UsernameChangeController extends Controller
{
    protected $login_suggestion;

    public function __construct(LoginSuggestion $login_suggestion) {
        $this->login_suggestion = $login_suggestion;
    }

    public function index(Request $request) {
        $user = $request->user();
        
        // Check if username change is currently allowed
        $restrictions = $this->getLoginRestrictions($user);
        
        return view('username_change.index', [
            'user' => $user,
            'login_validator' => config('profile.login_validator'),
            'suggested_login' => $request->session()->get('suggested_login'),
            'login_fixed' => $restrictions['login_fixed'],
            'login_change_restricted' => $restrictions['login_change_restricted'],
            'restriction_end_date' => $restrictions['restriction_end_date']
        ]);
    }

    public function update(Request $request) {
        $user = $request->user();
        $new_login = $request->get('login');
        
        $restrictions = $this->getLoginRestrictions($user);
        
        if($restrictions['login_fixed']) {
            return redirect()->back()->withErrors(['login' => trans('profile.login_fixed')]);
        }
        
        if($restrictions['login_change_restricted']) {
            return redirect()->back()->withErrors(['login' => trans('profile.login_change_restricted', [
                'date' => $restrictions['restriction_end_date']->format('d/m/Y')
            ])]);
        }
        
        if(!$new_login || $user->login === $new_login) {
            return redirect()->back()->withErrors(['login' => trans('validation.required', ['attribute' => 'login'])]);
        }
        
        $suggested_login = $this->login_suggestion->get($new_login);
        if($suggested_login !== $new_login) {
            return redirect()->back()->withInput()->with('suggested_login', $suggested_login);
        }
        
        $rules = SchemaConfig::login($user);
        $this->validate($request, [
            'login' => $rules['valid']
        ]);
        
        if($user->login) {
            UsernameChange::create([
                'user_id' => $user->id,
                'old_login' => $user->login
            ]);
        }
        
        // Update the username
        $user->login = $new_login;
        $user->save();
        
        return redirect('/profile')->with('success', trans('profile.login_updated'));
    }
    
    protected function getLoginRestrictions($user) {
        $login_fixed = $user->login_fixed;
        $login_change_restricted = false;
        $restriction_end_date = null;
        
        if(!is_null($user->login) && !is_null($user->login_updated_at) && $login_change_available = config('profile.login_change_available')) {
            $restriction_end_date = (new \DateTime($user->login_updated_at))->add(new \DateInterval($login_change_available['second_interval']));
            $now = new \DateTime;
            $login_change_restricted = $now < $restriction_end_date;
        }
        
        return [
            'login_fixed' => $login_fixed,
            'login_change_restricted' => $login_change_restricted,
            'restriction_end_date' => $restriction_end_date
        ];
    }
}
