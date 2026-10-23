<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('company members can view the member list', function (CompanyRole $role) {
    $user = User::factory()->create();
    $company = Company::factory()->create();

    $company->users()->attach($user->id, ['role' => $role->value]);

    $this->actingAs($user)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('company.id', $company->id)
            ->has('members.data', 1)
            ->where('members.data.0.id', $user->id)
            ->where('members.data.0.name', $user->name)
            ->where('members.data.0.email', $user->email)
            ->where('members.data.0.role', $role->value)
            ->missing('members.data.0.password')
            ->missing('members.data.0.two_factor_secret')
        );
})->with([
    'owner' => CompanyRole::Owner,
    'admin' => CompanyRole::Admin,
    'member' => CompanyRole::Member,
]);

test('the member list only include members of the requested company', function () {
    $user = User::factory()->create();
    $colleague = User::factory()->create();
    $outsider = User::factory()->create();

    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $company->users()->attach([
        $user->id => ['role' => CompanyRole::Owner->value],
        $colleague->id => ['role' => CompanyRole::Member->value],
    ]);

    $otherCompany->users()->attach([
        $outsider->id, ['role' => CompanyRole::Owner->value],
    ]);

    $this->actingAs($user)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('members.total', 2)
            ->has('members.data', 2)
            ->where('members.data',
                fn ($members) => collect($members)
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->all() === collect([$user->id, $colleague->id])->sort()->values()->all()
            )
        );
});

test('owning another company does not allow viewing its member list', function () {
    $user = User::factory()->create();
    $ownedCompany = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $ownedCompany->users()->attach($user->id, ['role' => CompanyRole::Owner->value]);

    $this->actingAs($user)
        ->get(route('companies.members.index', [
            'company' => $otherCompany->slug,
        ]))
        ->assertForbidden();
});

test('guests cannot view company members', function () {
    $company = Company::factory()->create();

    $this->get(route('companies.members.index', [
        'company' => $company->slug,
    ]))->assertRedirect(route('login'));
});

test('company members are paginated', function () {
    $company = Company::factory()->create();

    $owner = User::factory()->create([
        'name' => 'AAA Owner',
    ]);

    $company->users()->attach($owner->id, ['role' => CompanyRole::Owner->value]);

    $members = User::factory()->count(20)->create([
        'name' => 'ZZZ Member',
    ]);

    foreach ($members as $member) {
        $company->users()->attach($member->id, ['role' => CompanyRole::Member->value]);
    }

    $this->actingAs($owner)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('members.total', 21)
            ->where('members.per_page', 20)
            ->where('members.current_page', 1)
            ->where('members.last_page', 2)
            ->has('members.data', 20)
            ->where('members.data.0.id', $owner->id)
        );

    $this->get(route('companies.members.index', [
        'company' => $company->slug,
        'page' => 2,
    ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('members.current_page', 2)
            ->has('members.data', 1)
            ->where('members.data.0.id', $members->last()->id)
        );
});

test('members can be searched by name or email', function (string $search) {
    $company = Company::factory()->create();

    $owner = User::factory()->create([
        'name' => 'Miguel Costa',
        'email' => 'miguel@example.com',
    ]);

    $member = User::factory()->create([
        'name' => 'Ana Silva',
        'email' => 'support@example.com',
    ]);

    $company->users()->attach([
        $owner->id => ['role' => CompanyRole::Owner->value],
        $member->id => ['role' => CompanyRole::Member->value],
    ]);

    $this->actingAs($owner)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
            'search' => $search,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('filters.search', $search)
            ->where('members.total', 1)
            ->has('members.data', 1)
            ->where('members.data.0.id', $member->id)
        );

})->with([
    'name' => 'Ana',
    'email' => 'support',
]);

test('search does not return matching members from other companies', function () {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();

    $owner = User::factory()->create([
        'name' => 'Miguel Costa',
        'email' => 'miguel@example.com',
    ]);

    $outsider = User::factory()->create([
        'name' => 'Joana Silva',
        'email' => 'external-support@example.com',
    ]);

    $company->users()->attach([
        $owner->id => ['role' => CompanyRole::Owner->value],
    ]);

    $otherCompany->users()->attach([
        $outsider->id => ['role' => CompanyRole::Member->value],
    ]);

    $this->actingAs($owner)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
            'search' => 'external-support',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('members.total', 0)
            ->has('members.data', 0)
        );
});

test('an empty search returns all company members', function () {
    $company = Company::factory()->create();
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $company->users()->attach([
        $owner->id => ['role' => CompanyRole::Owner->value],
        $member->id => ['role' => CompanyRole::Member->value],
    ]);

    $this->actingAs($owner)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
            'search' => '   ',
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('filters.search', '')
            ->where('members.total', 2)
            ->has('members.data', 2)
        );
});

test('search results are paginated and pagination links preserve the search', function () {
    $company = Company::factory()->create();

    $owner = User::factory()->create([
        'name' => 'AAA Owner',
        'email' => 'owner@example.com',
    ]);

    $company->users()->attach($owner->id, ['role' => CompanyRole::Owner->value]);

    $members = User::factory()->count(21)->create([
        'name' => 'Support Member',
    ]);

    foreach ($members as $member) {
        $company->users()->attach($member->id, ['role' => CompanyRole::Member->value]);
    }

    $response = $this->actingAs($owner)
        ->get(route('companies.members.index', [
            'company' => $company->slug,
            'search' => 'Support',
        ]));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('filters.search', 'Support')
            ->where('members.total', 21)
            ->where('members.current_page', 1)
            ->where('members.last_page', 2)
            ->has('members.data', 20)
        );

    $nextPageUrl = $response->inertiaProps('members.next_page_url');

    expect($nextPageUrl)->toBeString();

    parse_str(parse_url($nextPageUrl, PHP_URL_QUERY), $query);

    expect($query)
        ->toHaveKey('search', 'Support')
        ->toHaveKey('page', 2);

    $this->get($nextPageUrl)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('companies/members/Index')
            ->where('filters.search', 'Support')
            ->where('members.total', 21)
            ->where('members.current_page', 2)
            ->has('members.data', 1)
            ->where('members.data.0.id', $members->last()->id)
        );
});
