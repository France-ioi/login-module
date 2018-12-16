<?php

namespace App\Http\Controllers\Admin\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserHelper;
use App\Client;
use App\LoginModule\Profile\SchemaBuilder;
use App\Http\Requests\Admin\StoreUserHelperRequest;

class UserHelperController extends Controller
{



    public function show($id) {
        $user_attributes = array_diff(
            SchemaBuilder::availableAttributes(),
            ['login', 'first_name', 'last_name']
        );
        $user = User::findOrFail($id);
        $user_helper = $user->userHelper ? $user->userHelper : new UserHelper;
        return view('admin.users.user_helper', [
            'user' => $user,
            'user_helper' => $user_helper,
            'clients' => Client::get(),
            'user_helper_clients' => $user->userHelperClients->pluck('id', 'id'),
            'user_attributes' => $user_attributes
        ]);

    }



    public function store($id, StoreUserHelperRequest $request) {
        $user = User::findOrFail($id);

        $user_helper = $user->userHelper;
        if($user_helper) {
            $user_helper->fill($request->all());
            $user_helper->save();
        } else {
            $user_helper = new UserHelper($request->all());
            $user->userHelper()->save($user_helper);
        }
        $user->userHelperClients()->sync($request->get('clients'));
        return redirect('/admin/users')->with('status', 'User helper settings saved');
    }

}
