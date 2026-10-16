<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('owner and admin can update the company name', function (CompanyRole $role) {
    $user = User::factory()->create();
    $company = Company::factory()->create();
    $originalSlug = $company->slug;

    $company->users()->attach($user->id, ['role' => $role->value]);

    $this->actingAs($user)
        ->patch(route('companies.update', [
            'company' => $company->slug,
        ]), [
            'name' => 'New Name',
            'slug' => 'slug-that-cannot-be-saved',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('companies.show', [
            'company' => $originalSlug,
        ]));

    $company->refresh();

    expect($company->name)->toBe('New Name')
        ->and($company->slug)->toBe($originalSlug);
})->with([
    'owner' => CompanyRole::Owner,
    'admin' => CompanyRole::Admin,
]);

test('members cannot update their company name', function () {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $company->users()->attach($user->id, ['role' => CompanyRole::Member->value]);

    $originalName = $company->name;

    $this->actingAs($user)
        ->patch(route('companies.update', [
            'company' => $company->slug,
        ]), [
            'name' => 'New Name',
        ])
        ->assertForbidden();

    expect($company->fresh()->name)->toBe($originalName);
});

test('owning one company does not allow updating another', function () {
    $user = User::factory()->create();
    $ownCompany = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $ownCompany->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

    $originalName = $otherCompany->name;

    $this->actingAs($user)
        ->patch(route('companies.update', [
            'company' => $otherCompany->slug,
        ]), [
            'name' => 'New Name',
        ])
        ->assertForbidden();

    expect($otherCompany->fresh()->name)->toBe($originalName);
});

test('guests cannot update a company', function () {
    $company = Company::factory()->create();
    $originalName = $company->name;

    $this->patch(route('companies.update', [
        'company' => $company->slug,
    ]), [
        'name' => 'New Name',
    ])
        ->assertRedirect(route('login'));

    expect($company->fresh()->name)->toBe($originalName);
});

test('invalid names are rejected when updating a company', function (mixed $name) {
    $user = User::factory()->create();
    $company = Company::factory()->create();
    $originalName = $company->name;

    $company->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

    $this->actingAs($user)
        ->patch(route('companies.update', [
            'company' => $company->slug,
        ]), [
            'name' => $name,
        ])
        ->assertSessionHasErrors('name');

    expect($company->fresh()->name)->toBe($originalName);
})->with([
    'missing name' => null,
    'empty name' => '',
    'only spaces' => '    ',
    'invalid type' => 123,
    'name too long' => str_repeat('a', 256),
]);

test('company details include the correct update permissions', function (
    CompanyRole $role,
    bool $canUpdate,
) {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $company->users()->attach($user->id, ['role' => $role->value]);

    $this->actingAs($user)
        ->get(route('companies.show', [
            'company' => $company->slug,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/Show')
            ->where('can.update', $canUpdate)
        );
})->with([
    'owner' => [CompanyRole::Owner, true],
    'admin' => [CompanyRole::Admin, true],
    'member' => [CompanyRole::Member, false],
]);
