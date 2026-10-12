<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login when accessing companies', function () {
    $this->get(route('companies.index'))
        ->assertRedirect(route('login'));
});

test('users onle see their own companies and memebership roles', function () {
    $user = User::factory()->create();

    $atlas = Company::factory()->create([
        'name' => 'Atlas Digital',
    ]);

    $horizonte = Company::factory()->create([
        'name' => 'Horizonte Studio',
    ]);

    // This company exists but not belongs to the user
    Company::factory()->create([
        'name' => 'Other Company',
    ]);

    $user->companies()->attach($atlas->id, [
        'role' => CompanyRole::Member->value,
    ]);

    $user->companies()->attach($horizonte->id, [
        'role' => CompanyRole::Owner->value,
    ]);

    $this->actingAs($user)
        ->get(route('companies.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/Index')
            ->has('companies', 2)
            ->where('companies.0.id', $atlas->id)
            ->where('companies.0.name', $atlas->name)
            ->where('companies.0.slug', $atlas->slug)
            ->where('companies.0.role', 'member')
            ->where('companies.1.id', $horizonte->id)
            ->where('companies.1.role', 'owner')
        );
});

test('users without companies receive an empty lists', function () {
    $user = User::factory()->create();

    Company::factory()->create();

    $this->actingAs($user)
        ->get(route('companies.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/Index')
            ->has('companies', 0)
        );
});
