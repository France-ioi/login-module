<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OriginInstance;
use App\Http\Requests\Admin\StoreOriginInstanceRequest;

class OriginInstancesController extends Controller
{

    public function index(Request $request)
    {
        $query = OriginInstance::query();
        return view('admin.origin_instances.index', [
            'models' => $query->get()
        ]);
    }


    public function create()
    {
        return view('admin.origin_instances.form', [
            'origin_instance' => new OriginInstance
        ]);
    }


    public function store(StoreOriginInstanceRequest $request)
    {
        OriginInstance::create($request->all());
        return redirect()
            ->route('admin.origin_instances.index')
            ->with('status', 'New instance added.');
    }


    public function edit(OriginInstance $origin_instance)
    {
        return view('admin.origin_instances.form', [
            'origin_instance' => $origin_instance,
        ]);
    }


    public function update(StoreOriginInstanceRequest $request, OriginInstance $origin_instance)
    {
        $origin_instance->fill($request->all());
        $origin_instance->save();
        return redirect()
            ->route('admin.origin_instances.index')
            ->with('status', 'Instance updated.');
    }

}
