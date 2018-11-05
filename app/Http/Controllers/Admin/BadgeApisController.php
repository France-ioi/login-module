<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BadgeApi;
use App\Http\Requests\Admin\StoreBadgeApiRequest;


class BadgeApisController extends Controller
{


    public function index(Request $request)
    {
        $query = BadgeApi::query();
        return view('admin.badge_apis.index', [
            'badge_apis' => $query->paginate()
        ]);
    }


    public function create()
    {
        return view('admin.badge_apis.form', [
            'badge_api' => new BadgeApi
        ]);
    }


    public function store(StoreBadgeApiRequest $request)
    {
        BadgeApi::create($request->all());
        return redirect()
            ->route('admin.badge_apis.index')
            ->with('status', 'New Record added.');
    }


    public function show(BadgeApi $cfg)
    {
        //
    }


    public function edit(BadgeApi $badge_api)
    {
        return view('admin.badge_apis.form', [
            'badge_api' => $badge_api
        ]);
    }


    public function update(StoreBadgeApiRequest $request, BadgeApi $badge_api)
    {
        $badge_api->fill($request->all());
        $badge_api->save();
        return redirect()
            ->route('admin.badge_apis.index')
            ->with('status', 'Record updated.');
    }


    public function destroy(BadgeApi $badge_api)
    {
        $badge_api->delete();
        return redirect()
            ->route('admin.badge_apis.index')
            ->with('status', 'Record deleted.');
    }
}
