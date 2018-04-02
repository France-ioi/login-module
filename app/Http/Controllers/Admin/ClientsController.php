<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreClientRequest;
use App\LoginModule\Profile\SchemaBuilder;
use App\LoginModule\AuthList;
use App\VerificationMethod;
use App\LoginModule\Profile\Verification\Verification;

class ClientsController extends Controller
{

    public function __construct(AuthList $auth_list) {
        $this->auth_list = $auth_list;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.clients.index', [
            'clients' => Client::get()
        ]);       //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $client = new Client([
            'secret' => str_random(40)
        ]);
        return view('admin.clients.form', [
            'client' => $client,
            'user_attributes' => SchemaBuilder::availableAttributes(),
            'verifiable_attributes' => Verification::ATTRIBUTES,
            'auth_methods' => $this->auth_list->all(),
            'verification_methods' => VerificationMethod::get(),
            'client_verification_methods' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        $client = new Client($request->all());
        $client->personal_access_client = false;
        $client->password_client = false;
        $auth_order = $request->has('auth_order') ? $request->get('auth_order') : [];
        $client->auth_order = $this->auth_list->normalize($auth_order);
        $client->attributes_filter = $this->cleanAtrributesfilter($request);
        $client->save();
        $this->syncVerificationMethods($client, $request);
        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'New client added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('admin.clients.form', [
            'client' => $client,
            'user_attributes' => SchemaBuilder::availableAttributes(),
            'verifiable_attributes' => Verification::ATTRIBUTES,
            'auth_methods' => $this->auth_list->normalize($client->auth_order),
            'verification_methods' => VerificationMethod::get(),
            'client_verification_methods' => $client->verification_methods->pluck('pivot', 'id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(StoreClientRequest $request, Client $client)
    {
        $client->fill($request->all());
        $auth_order = $request->has('auth_order') ? $request->get('auth_order') : [];
        $client->auth_order = $this->auth_list->normalize($auth_order);
        $client->attributes_filter = $this->cleanAtrributesfilter($request);
        $client->save();
        $this->syncVerificationMethods($client, $request);
        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client deleted.');
    }


    private function syncVerificationMethods($client, $request) {
        $data = [];
        $methods = $request->get('verification_methods');
        $expiration = $request->get('verification_methods_expiration');
        foreach($methods as $id) {
            $data[$id] = [
                'expiration' => isset($expiration[$id]) ? (int) $expiration[$id] : null
            ];
        }
        $client->verification_methods()->sync($data);
    }


    private function cleanAtrributesfilter($request) {
        $res = [];
        foreach($request->get('attributes_filter') as $key => $value) {
            if($value === null || trim($value) == '') continue;
            $res[$key] = $value;
        }
        return $res;
    }

}