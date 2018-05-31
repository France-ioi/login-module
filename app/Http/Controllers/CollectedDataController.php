<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Profile\SchemaBuilder;

class CollectedDataController extends Controller
{

    public function index(Request $request) {
        return view('collected_data.index', [
            'clients' => $this->clients($request->user())
        ]);
    }


    public function export(Request $request) {
        $data = $this->data($request->user());
        return response(
            json_encode($data),
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="login-module-export.json"',
            ]
        );
    }


    public function summary(Request $request) {
        return view('collected_data.summary', [
            'data' => $this->data($request->user())
        ]);
    }


    public function delete(Request $request) {
        $user = \User::find($request->user()->id);
        \Auth::logout();
        $user->delete();
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
