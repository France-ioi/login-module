<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LtiConfig;
use App\Http\Requests\Admin\StoreLtiConfigRequest;


class LtiConfigsController extends Controller
{


    public function index(Request $request)
    {
        $query = LtiConfig::query();
        return view('admin.lti_configs.index', [
            'lti_configs' => $query->paginate()
        ]);
    }


    public function create()
    {
        return view('admin.lti_configs.form', [
            'lti_config' => new LtiConfig
        ]);
    }


    public function store(StoreLtiConfigRequest $request)
    {
        LtiConfig::create($request->all());
        return redirect()
            ->route('admin.lti_configs.index')
            ->with('status', 'New consumer added.');
    }


    public function show(LtiConfig $cfg)
    {
        //
    }


    public function edit(LtiConfig $lti_config)
    {
        return view('admin.lti_configs.form', [
            'lti_config' => $lti_config
        ]);
    }


    public function update(StoreLtiConfigRequest $request, LtiConfig $lti_config)
    {
        $lti_config->fill($request->all());
        $lti_config->save();
        return redirect()
            ->route('admin.lti_configs.index')
            ->with('status', 'Consumer updated.');
    }


    public function destroy(LtiConfig $lti_config)
    {
        $lti_config->delete();
        return redirect()
            ->route('admin.lti_configs.index')
            ->with('status', 'Consumer deleted.');
    }
}
