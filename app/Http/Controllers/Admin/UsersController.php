<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Email;
use App\LoginModule\Badges;
use App\LoginModule\Platform\BadgeApi;
use Illuminate\Support\Facades\Password;
use Mail;

class UsersController extends Controller
{

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
        return view('admin.users.index', [
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
        return view('admin.users.password', [
            'user' => User::findOrFail($id)
        ]);
    }


    public function updatePassword($id, Request $request) {
        User::findOrFail($id)->update([
            'password' => \Hash::make($request->input('password'))
        ]);
        return redirect('/admin/users')->with('status', 'Password changed');
    }


    public function showTeacherStatus($id) {
        return view('admin.users.teacher_status', [
            'user' => User::findOrFail($id)
        ]);
    }


    public function updateTeacherStatus($id, Request $request) {
        $user = User::findOrFail($id);
        $user->teacher_verified = $request->has('teacher_verified');
        $user->save();
        return redirect('/admin/users')->with('status', 'Teacher status saved');
    }



    public function showEmails($id) {
        $user = User::with('emails')->findOrFail($id);
        return view('admin.users.emails', [
            'user' => $user
        ]);
    }


    public function createResetLink(Request $request) {
        $email = Email::findOrFail($request->input('email_id'));
        $token = Password::broker()->createToken($email);
        $body =
            'Follow this link to reset your password:'.PHP_EOL.
            route('password.reset', $token);
        $subject = 'Password reset';
        return view('admin.users.send_reset_link', [
            'subject' => $subject,
            'body' => $body,
            'email' => $email
        ]);
    }


    public function sendResetLink(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'subject' => 'required',
            'body' => 'required',
        ]);
        Mail::raw($request->input('body'), function ($message) use ($request) {
            $message->to($request->input('email'))->subject($request->input('subject'));
        });
        return redirect('/admin/users')->with('status', 'Password recovery email was sent');
    }

}