<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BadgesController extends Controller
{

    public function getInfos(Request $request) {
        $this->validate($request, [
            'badgeUrl' => 'required',
            'verifInfos' => 'required',
            'verifType' => 'required'
        ]);

    }


    public function confirmAccountCreation(Request $request) {
        $this->validate($request, [
            'badgeUrl' => 'required',
            'verifInfos' => 'required',
            'verifType' => 'required',
            'userInfos' => 'required'
        ]);

    }


    public function attachBadge(Request $request) {
        $this->validate($request, [
            'badgeUrl' => 'required',
            'verifInfos' => 'required',
            'verifType' => 'required'
        ]);

    }


    public function iDontHaveThisBadge(Request $request) {
        $this->validate($request, [
            'badgeUrl' => 'required',
        ]);
        
    }


}
