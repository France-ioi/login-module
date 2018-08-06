<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OriginInstance;
use App\User;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Migrators\Merge\Group;

class MergingAccountsController extends Controller
{

    protected $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function index(Request $request) {
        //dd($request->session()->all());
        //if($request->user()->origin_)
        $user = $request->user();
        if(!$group_user = $this->getGroupedUser($user)) {
            return redirect($this->context->continueUrl('/account'));
        }

        return view('merging_accounts.index', [
            'instance_name' => $group_user->origin_instance_id ? OriginInstance::find($group_user->origin_instance_id)->name : trans('merging_accounts.this_instance'),
            'similar_fields' => $this->getSimilarFields($user, $group_user)
        ]);
    }


    public function acceptMerge(Request $request) {
        if(!$group_user = $this->getGroupedUser($request->user())) {
            return redirect($this->context->continueUrl('/account'));
        }

        $merge_account_id = $request->session()->get('merge_account_id');
        if($merge_account_id && $request->user()->id === $merge_account_id)  {
            $request->session()->forget('merge_account_id');
            Group::mergeUsers($request->user(), $group_user);
            return redirect($this->context->continueUrl('/account'));
        }

        $context_data = $this->context->getData();
        $request->session()->flush();
        $request->session()->regenerate();
        $request->session()->put('merge_account_id', $group_user->id);
        $this->context->setData($context_data);
        return redirect('/auth'); // ??
    }


    public function declineMerge(Request $request) {
        $request->session()->forget('merge_account_id');
        if($group_user = $this->getGroupedUser($request->user())) {
            Group::reqireRevalidation($request->user(), collect([$group_user]));
        }
        return redirect($this->context->continueUrl('/account'));
    }


    private function getGroupedUser($user) {
        if(!$user->merge_group_id) {
            return null;
        }
        return User::where('merge_group_id', $user->merge_group_id)->where('id', '<>', $user->id)->first();
    }


    private function getSimilarFields($user, $group_user) {
        $res = [];
        if(!is_null($user->login) && $user->login === $group_user->login) {
            $res[] = $user->login;
        }
        if($user->primary_email_id && $group_user->primary_email_id && $user->primary_email === $group_user->primary_email) {
            $res[] = $user->primary_email;
        }
        if($user->secondary_email_id && $group_user->secondary_email_id && $user->secondary_email === $group_user->secondary_email) {
            $res[] = $user->secondary_email;
        }
        return $res;
    }

}
