<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\ValidationRequest;
use App\Models\Company;
use App\Models\Device;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Company::withCount("devices")->with("devices")->orderByDesc("id")->paginate(request("per_page", 10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValidationRequest $request)
    {
        return Company::create($request->validated());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(ValidationRequest $request, Company $company)
    {
        $company->update($request->validated());

        return $company;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();

        Device::where("company_id", $company->id)->delete();

        return response()->noContent();
    }
}
