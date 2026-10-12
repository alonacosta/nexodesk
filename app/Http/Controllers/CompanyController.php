<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
}
