<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login when opening a company', function () {
    $company = Company::factory()->create();

    $this->get(route('companies.show', [
        'company' => $company->slug,
    ]))->assertRedirect(route('login'));
});

test('company members can view their company', function (CompanyRole $role) {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $company->users()->attach($user->id, [
        'role' => $role->value,
    ]);

    $this->actingAs($user)
        ->get(route('companies.show', [
            'company' => $company->slug,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/Show')
            ->where('company.id', $company->id)
            ->where('company.name', $company->name)
            ->where('company.slug', $company->slug)
        );
})->with([
    'owner' => CompanyRole::Owner,
    'admin' => CompanyRole::Admin,
    'member' => CompanyRole::Member,
]);

test('users cannot view a company they do not belong to', function () {
    $user = User::factory()->create();

    $ownCompany = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $ownCompany->users()->attach($user->id, [
        'role' => CompanyRole::Owner->value,
    ]);

    $this->actingAs($user)
        ->get(route('companies.show', [
            'company' => $otherCompany->slug,
        ]))
        ->assertForbidden();
});

test('a nonexistent company returns not found', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('companies.show', [
            'company' => 'nonexistent-company',
        ]))
        ->assertNotFound();
});
