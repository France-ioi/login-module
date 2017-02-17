<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{

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
        User::findOrFail($id)->delete();
        return redirect()->back()->with('message', 'User deleted');
    }


    public function show_password($id) {
        return view('admin.user_password', [
            'user' => User::findOrFail($id)
        ]);
    }

    public function update_password($id, Request $request) {
        User::findOrFail($id)->update([
            'password' => \Hash::make($request->input('password'))
        ]);
        return redirect('/admin/users')->with('message', 'Password changed');
    }

}
