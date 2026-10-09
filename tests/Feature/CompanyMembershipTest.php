<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Database\QueryException;

test('a user can belong to multiple companies with different roles', function () {
    $user = User::factory()->create();

    $firstCompany = Company::create([
        'name' => 'Atlas Digital',
        'slug' => 'atlas-digital',
    ]);

    $secondCompany = Company::create([
        'name' => 'Horizonte Studio',
        'slug' => 'horizonte-studio',
    ]);

    $user->companies()->attach($firstCompany->id, [
        'role' => CompanyRole::Owner->value,
    ]);

    $user->companies()->attach($secondCompany->id, [
        'role' => CompanyRole::Member->value,
    ]);

    $companies = $user->companies()->get();

    expect($companies)->toHaveCount(2);

    $firstMembership = $companies->firstWhere('id', $firstCompany->id);
    $secondMembership = $companies->firstWhere('id', $secondCompany->id);

    expect($firstMembership->pivot)
        ->toBeInstanceOf(CompanyUser::class)
        ->and($firstMembership->pivot->role)->toBe(CompanyRole::Owner)
        ->and($secondMembership->pivot->role)->toBe(CompanyRole::Member);
});

test('a company can retrieve its members and their membership details', function () {
    $company = Company::create([
        'name' => 'Atlas Digital',
        'slug' => 'atlas-digital',
    ]);

    $user = User::factory()->create();

    $company->users()->attach($user->id, [
        'role' => CompanyRole::Admin->value,
    ]);

    $member = $company->users()->firstOrFail();

    expect($member->id)->toBe($user->id)
        ->and($member->pivot)->toBeInstanceOf(CompanyUser::class)
        ->and($member->pivot->role)->toBe(CompanyRole::Admin)
        ->and($member->pivot->company_id)->toBe($company->id)
        ->and($member->pivot->user_id)->toBe($user->id)
        ->and($member->pivot->created_at)->not->toBeNull()
        ->and($member->pivot->updated_at)->not->toBeNull();
});

test('a user cannot belong to the sane company twice', function () {
    $company = Company::create([
        'name' => 'Atlas Digital',
        'slug' => 'atlas-digital',
    ]);

    $user = User::factory()->create();

    $company->users()->attach($user->id, [
        'role' => CompanyRole::Member->value,
    ]);

    expect(fn () => $company->users()->attach($user->id, [
        'role' => CompanyRole::Admin->value,
    ]))->toThrow(QueryException::class);
});
