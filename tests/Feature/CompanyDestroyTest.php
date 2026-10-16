<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;

test('the owner can delete company without deleting its users', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $company->users()->attach($owner->id, ['role' => CompanyRole::Owner->value]);
    $company->users()->attach($member->id, ['role' => CompanyRole::Member->value]);

    $otherCompany->users()->attach($member->id, ['role' => CompanyRole::Member->value]);

    $this->actingAs($owner)
        ->delete(route('companies.destroy', [
            'company' => $company->slug,
        ]))
        ->assertRedirect(route('companies.index'));

    $this->assertModelMissing($company);

    $this->assertDatabaseMissing('company_user', [
        'company_id' => $company->id,
    ]);

    $this->assertModelExists($owner);
    $this->assertModelExists($member);
    $this->assertModelExists($otherCompany);

    $this->assertDatabaseHas('company_user', [
        'company_id' => $otherCompany->id,
        'user_id' => $member->id,
    ]);
});

test('admins and members cannot delete a company', function (CompanyRole $role) {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $company->users()->attach($user->id, ['role' => $role->value]);

    $this->actingAs($user)
        ->delete(route('companies.destroy', [
            'company' => $company->slug,
        ]))
        ->assertForbidden();

    $this->assertModelExists($company);

    $this->assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role' => $role->value,
    ]);
})->with([
    'admin' => CompanyRole::Admin,
    'member' => CompanyRole::Member,
]);

test('owning another company does not allow deleting this company', function () {
    $user = User::factory()->create();
    $ownedCompany = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $ownedCompany->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

    $this->actingAs($user)
        ->delete(route('companies.destroy', [
            'company' => $otherCompany->slug,
        ]))
        ->assertForbidden();

    $this->assertModelExists($ownedCompany);
    $this->assertModelExists($otherCompany);
});

test('guests cannot delete a company', function () {
    $company = Company::factory()->create();

    $this->delete(route('companies.destroy', [
        'company' => $company->slug,
    ]))
        ->assertRedirect(route('login'));

    $this->assertModelExists($company);
});
