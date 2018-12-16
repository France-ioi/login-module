<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserHelperAction;

class UserHelperLogController extends Controller
{

    public function index(Request $request) {
        $q = UserHelperAction::query();
        if($request->has('user_id')) {
            $q->where('user_id', $request->get('user_id'));
        }
        if($request->has('target_user_id')) {
            $q->where('target_user_id', $request->get('target_user_id'));
        }
        return view('admin.user_helper_log.index', [
            'items' => $q->paginate(15)
        ]);
    }



    public function details($id) {
        return view('admin.user_helper_log.details', [
            'item' => UserHelperAction::findOrFail($id)
        ]);
    }
}
