<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Profile\SchemaBuilder;
use App\User;

class CollectedDataController extends Controller
{

    public function index(Request $request) {
        return view('collected_data.index', [
            'clients' => $this->clients($request->user()),
            'data' => $this->data($request->user())
        ]);
    }


    public function export(Request $request) {
        $data = $this->data($request->user());
        return response(
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="login-module-export.json"',
            ]
        );
    }


    public function delete(Request $request) {
        if($this->clients($request->user())->count() > 0) {
            return redirect()->back()->withErrors([
                'clients_linked' => trans('collected_data.alert')
            ]);
        }
        $user = User::findOrFail($request->user()->id);
        $user->accessTokenCounters()->delete();
        $user->authConnections()->delete();
        $user->autoLoginToken()->delete();
        $user->badges()->delete();
        $user->emails()->delete();
        $user->obsoletePasswords()->delete();
        $user->verifications()->delete();
        \Auth::logout();
        $user->clearUserDataAttributes();
        $user->save();
        return redirect('/');
    }


    private function data($user) {
        $attributes = SchemaBuilder::availableAttributes();
        $res = [];
        foreach($attributes as $attribute) {
            $res[] = (object) [
                'name' => $attribute,
                'title' => trans('profile.'.$attribute),
                'value' => $user->$attribute
            ];
        }
        return $res;
    }


    private function clients($user) {
        $ids = \Laravel\Passport\Token::where('user_id', $user->id)
            ->distinct()
            ->get(['client_id'])
            ->pluck('client_id');
        return \App\Client::whereIn('id', $ids)->get();
    }
}
