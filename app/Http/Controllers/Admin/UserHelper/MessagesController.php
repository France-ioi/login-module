<?php

namespace App\Http\Controllers\Admin\UserHelper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends UserHelperController
{

    public function index($message) {
        return view('admin.user_helper.messages.'.$message);
    }

}