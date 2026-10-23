<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CompanyMemberController extends Controller
{
    public function index(Company $company, Request $request): Response
    {
        Gate::authorize('view', $company);

        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $search = $request->string('search')->trim()->toString();

        $members = $company->users()
            ->select(['users.id', 'users.name', 'users.email'])
            ->search($search)
            ->orderBy('users.name')
            ->orderBy('users.id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->pivot->role->value,
            ]);

        return Inertia::render('companies/members/Index', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'slug' => $company->slug,
            ],
            'members' => $members,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }
}
