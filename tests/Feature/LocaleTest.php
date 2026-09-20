<?php

use Inertia\Testing\AssertableInertia as Assert;

test('guest can change their locale', function (string $locale) {
    $this->from(route('home'))
        ->patch(route('locale.update'), [
            'locale' => $locale,
        ])
        ->assertRedirect(route('home'))
        ->assertSessionHas('locale', $locale);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('locale', $locale)
        );
})->with(['en', 'pt-PT']);

test('unsupported locales are rejected without changing the saved locale', function () {
    $this->withSession(['locale' => 'pt-PT'])
        ->from(route('home'))
        ->patch(route('locale.update'), [
            'locale' => 'invalid',
        ])
        ->assertSessionHasErrors(['locale'])
        ->assertSessionHas('locale', 'pt-PT');
});

test('the locale is required', function () {
    $this->from(route('home'))
        ->patch(route('locale.update'), [])
        ->assertSessionHasErrors(['locale']);
});

test('the application uses English when no locale is saved', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('locale', 'en')
        );
});

test('the middleware applies the Portuguese backend locale', function () {
    $this->withSession(['locale' => 'pt-PT'])
        ->get(route('home'))
        ->assertOk();

    expect(app()->getLocale())->toBe('pt');
});
