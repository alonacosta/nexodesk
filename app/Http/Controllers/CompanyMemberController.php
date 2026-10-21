<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CompanyMemberController extends Controller
{
    public function index(Company $company): Response
    {
        Gate::authorize('view', $company);

        $members = $company->users()
            ->select(['users.id', 'users.name', 'users.email'])
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(20)
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
        ]);
    }
}
