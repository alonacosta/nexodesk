<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;

test('guests cannot create companies', function () {
    $this->post(route('companies.store'), [
        'name' => 'Atlas Digital',
    ])->assertRedirect(route('login'));

    $this->assertDatabaseCount('companies', 0);
});

test('user can create a company e become its owner', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('companies.store'), [
            'name' => 'Atlas Digital',
        ]);

    $company = Company::query()->sole();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('companies.show', [
            'company' => $company->slug,
        ]));

    expect($company->name)->toBe('Atlas Digital')
        ->and($company->slug)->toStartWith('atlas-digital-');

    $this->assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role' => CompanyRole::Owner->value,
    ]);

    $this->assertDatabaseCount('company_user', 1);
});

test('company names must be valid', function (mixed $name) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('companies.store'), [
            'name' => $name,
        ])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('companies', 0);
    $this->assertDatabaseCount('company_user', 0);
})->with([
    'missing name' => null,
    'empty name' => '',
    'only spaces' => '    ',
    'invalid type' => 123,
    'name too long' => str_repeat('a', 256),
]);

test('companies can share a name but have different slugs', function () {
    $user = User::factory()->create();

    foreach (range(1, 2) as $attempt) {
        $this->actingAs($user)
            ->post(route('companies.store'), [
                'name' => 'Atlas Digital',
            ])
            ->assertSessionHasNoErrors()
            ->assertredirect();
    }

    $companies = Company::query()->get();

    expect($companies)->toHaveCount(2)
        ->and($companies->pluck('slug'))->unique()->toHaveCount(2)
        ->and($user->companies()->count())->toBe(2);
});

test('the creator and role cannot be overridden through submitted data', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->post(route('companies.store'), [
            'name' => 'Atlas Digital',
            'user_id' => $otherUser->id,
            'role' => CompanyRole::Admin->value,
            'slug' => 'slug-enviado-pelo-cliente',
        ])
        ->assertSessionHasNoErrors()
        ->assertredirect();

    $company = Company::query()->sole();

    expect($company->slug)->toStartWith('atlas-digital-');

    $this->assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role' => CompanyRole::Owner->value,
    ]);

    $this->assertDatabaseMissing('company_user', [
        'company_id' => $company->id,
        'user_id' => $otherUser->id,
    ]);
});
