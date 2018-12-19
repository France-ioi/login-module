<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\UserHelperAction;
use App\User;
use App\Client;

class SearchController extends UserHelperController
{


    public function index(Request $request) {
        if(!$this->searchAvailable($request)) {
            return view('admin.user_helper.errors.search_limit');
        }
        return view('admin.user_helper.index', [
            'items' => $this->findUsers($request)
        ]);
    }


    private function searchAvailable($request) {
        $user = $request->user();
        if($user->hasRole('admin') || !$request->has('keyword')) {
            return true;
        }
        $hash = $this->makeHash($request);
        $date = \Carbon\Carbon::now()->subHours(config('user_helper.limits_time_interval'));
        $search_exists = $user->userHelperActions
            ->where('created_at', '>', $date)
            ->where('type', 'search')
            ->where('hash', $hash)->first();
        if($search_exists) {
            return true;
        }
        $amount = $user->userHelperActions
            ->where('created_at', '>', $date)
            ->where('type', 'search')
            ->count();
        $res = $amount < $request->user()->userHelper->searches_amount;
        if($res) {
            $search = new UserHelperAction([
                'hash' => $hash,
                'type' => 'search',
                'details' => ['keyword' => $request->get('keyword')]
            ]);
            $user->userHelperActions()->save($search);
        }
        return $res;
    }


    private function makeHash($request) {
        return md5($request->get('keyword'));
    }



    private function findUsers($request) {
        $k = trim($request->get('keyword'));
        if(!$request->has('keyword') || $k == '') {
            return null;
        }
        $alowed_clients = $request->user()->userHelperClients->pluck('id');
        $restricted_clients = Client::where('user_helper_search_exclude', true)->get()->pluck('id');
        $k = '%'.$k.'%';
        return User::query()
            ->whereHas('accessTokenCounters', function ($q) use ($alowed_clients) {
                $q->whereIn('client_id', $alowed_clients);
            })
            ->whereDoesntHave('accessTokenCounters', function ($q) use ($restricted_clients) {
                $q->whereIn('client_id', $restricted_clients);
            })
            ->where(function($q) use ($k) {
                return $q->whereHas('emails', function($q) use ($k) {
                    $q->where('email', 'LIKE', $k);
                })
                ->orWhere('login', 'LIKE', $k)
                ->orWhere('first_name', 'LIKE', $k)
                ->orWhere('last_name', 'LIKE', $k);
            })
            ->limit(config('user_helper.search_results_length'))
            ->orderBy('last_login', 'desc')
            ->get();
    }

}