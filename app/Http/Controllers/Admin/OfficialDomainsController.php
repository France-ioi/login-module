<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OfficialDomain;
use App\Http\Requests\Admin\StoreOfficialDomainRequest;

class OfficialDomainsController extends Controller
{


    public function index(Request $request)
    {
        $query = OfficialDomain::query();
        if($request->get('country_code')) {
            $query->where('country_code', $request->get('country_code'));
        }
        if($request->get('domain')) {
            $query->where('domain', 'LIKE','%'.$request->get('domain').'%');
        }
        return view('admin.official_domains.index', [
            'countries' => trans('countries'),
            'models' => $query->paginate()
        ]);
    }


    public function create()
    {
        return view('admin.official_domains.form', [
            'official_domain' => new OfficialDomain
        ]);
    }


    public function store(StoreOfficialDomainRequest $request)
    {
        OfficialDomain::create($request->all());
        return redirect()
            ->route('admin.official_domains.index')
            ->with('status', 'New domain added.');
    }


    public function show(OfficialDomain $official_domain)
    {
        //
    }


    public function edit(OfficialDomain $official_domain)
    {
        return view('admin.official_domains.form', [
            'official_domain' => $official_domain,
        ]);
    }


    public function update(StoreOfficialDomainRequest $request, OfficialDomain $official_domain)
    {
        $official_domain->fill($request->all());
        $official_domain->save();
        return redirect()
            ->route('admin.official_domains.index')
            ->with('status', 'Domain updated.');
    }


    public function destroy(OfficialDomain $official_domain)
    {
        $official_domain->delete();
        return redirect()
            ->route('admin.official_domains.index')
            ->with('status', 'Domain deleted.');
    }
}
