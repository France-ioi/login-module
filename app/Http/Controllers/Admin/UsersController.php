<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Email;
use App\LoginModule\Badges;
use App\LoginModule\Platform\BadgeApi;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class UsersController extends Controller
{

    use SendsPasswordResetEmails;

    public function index(Request $request) {
        $query = User::query();
        if($request->get('id')) {
            $query->where('id', $request->get('id'));
        }
        if($request->get('login')) {
            $query->where('login', $request->get('login'));
        }
        if($request->get('email')) {
            $query->whereHas('emails', function($query) use ($request) {
                $query->where('email', $request->get('email'));
            });
        }
        return view('admin.users', [
            'users' => $query->paginate()
        ]);
    }


    public function delete($id) {
        $user = User::findOrFail($id);
        try {
            $user->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('status', 'User deleted');
    }


    public function showPassword($id) {
        return view('admin.user_password', [
            'user' => User::findOrFail($id)
        ]);
    }

    public function updatePassword($id, Request $request) {
        User::findOrFail($id)->update([
            'password' => \Hash::make($request->input('password'))
        ]);
        return redirect('/admin/users')->with('status', 'Password changed');
    }


    public function showEmails($id) {
        $user = User::with('emails')->findOrFail($id);
        return view('admin.user_emails', [
            'user' => $user
        ]);
    }

    public function sendResetLink(Request $request) {
        return $this->sendResetLinkEmail($request);
    }

}