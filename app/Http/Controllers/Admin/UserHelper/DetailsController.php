<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;

class DetailsController extends UserHelperController
{

    public function index($id, Request $request) {
        $user = $this->getTargetUser($id, $request);
        return view('admin.user_helper.details', [
            'user' => $user
        ]);
    }

}