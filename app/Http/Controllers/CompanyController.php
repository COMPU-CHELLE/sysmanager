<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Companies/Index', [
            'companies' => $request->user()->accessibleCompanies()
                ->when($request->user()->canRestore('companies'), fn (Builder $query): Builder => $query->withTrashed())
                ->orderBy('name')
                ->get()
                ->map(fn (Company $company): array => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                    'isActive' => $company->is_active,
                    'deletedAt' => $company->deleted_at?->toDateTimeString(),
                ]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        Company::create($request->validated());

        return Redirect::route('companies.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $company->update($request->validated());

        return Redirect::route('companies.index');
    }

    public function destroy(Request $request, int $company): RedirectResponse
    {
        $request->user()->accessibleCompanies()->findOrFail($company)->delete();

        return Redirect::route('companies.index');
    }

    public function restore(Request $request, int $company): RedirectResponse
    {
        $request->user()->accessibleCompanies()->onlyTrashed()->findOrFail($company)->restore();

        return Redirect::route('companies.index');
    }

    public function forceDelete(Request $request, int $company): RedirectResponse
    {
        $request->user()->accessibleCompanies()->onlyTrashed()->findOrFail($company)->forceDelete();

        return Redirect::route('companies.index');
    }
}
