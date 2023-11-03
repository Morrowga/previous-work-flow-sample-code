<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;

beforeEach(function () {
    // Run migrations
    Artisan::call('migrate:fresh');
    // Seed the database with test data
    Artisan::call('db:seed');
});

test('page builder   access for superadmin and back button to home', function () {

    $user = UserEloquentModel::where('email', 'superadmin@mail.com')->first();

    $this->actingAs($user);

    $this->assertAuthenticated(); // Check if the user is authenticated

    $this->get('/bc/admin');

    $response = $this->get('/home');
    $response->assertStatus(200);

});

test('page builder not superadmin access', function () {

    Auth::logout();
    $response = $this->get('/bc/admin');

    $response->assertRedirect('/login');

});

test('page builder access with other roles ', function () {
    Auth::logout();

    $user = UserEloquentModel::create([
        'role_id' => 2,
        'first_name' => 'testing',
        'last_name' => 'user',
        'email' => 'testing@gmail.com',
        'password' => 'password',
    ]);

    if (Auth::attempt(['email' => 'testing@gmail.com', 'password' => 'password'])) {

        $response = $this->get('/bc/admin');
        $response->assertStatus(403);
    }

    $response = $this->get('/bc/admin');
    $response->assertStatus(403);

});
