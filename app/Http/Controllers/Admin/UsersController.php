<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{

    public function index(Request $request) {
        if($request->has('id')) {
            $users = User::where('id', $request->input('id'))->paginate();
        } else {
            $users = User::paginate();
        }
        return view('admin.users', [
            'users' => $users
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
