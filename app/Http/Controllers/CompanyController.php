<?php

namespace App\Http\Controllers;

use App\Enums\CompanyRole;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        abort_if($user === null, 401);

        Gate::authorize('viewAny', Company::class);

        $companies = $user->companies()
            ->orderBy('name')
            ->get()
            ->map(fn (Company $company): array => [
                'id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
                'role' => $company->pivot->role->value,
            ]);

        return Inertia::render('companies/Index', [
            'companies' => $companies,
        ]);
    }

    public function show(Request $request, Company $company): Response
    {
        Gate::authorize('view', $company);

        return Inertia::render('companies/Show', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
            ],
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $name = $request->validated('name');

        $company = DB::transaction(function () use ($user, $name): Company {
            $baseSlug = Str::slug($name) ?: 'company';

            $company = Company::create([
                'name' => $name,
                'slug' => $baseSlug.'-'.Str::uuid(),
            ]);

            $company->users()->attach($user->id, [
                'role' => CompanyRole::Owner->value,
            ]);

            return $company;
        });

        return to_route('companies.show', [
            'company' => $company->slug,
        ]);
    }
}
