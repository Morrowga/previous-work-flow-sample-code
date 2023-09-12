<?php

namespace Tests\Feature\Authi;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\RoleEloquentModel;
use Src\BlendedConcept\Security\Infrastructure\EloquentModels\UserEloquentModel;

/**
 *  require
 *
 *  @return bool True if email is required
 */
// test('validation b2c register', function () {

//     $data = [
//         'email' => '',
//         'password' => 'password',
//     ];
//     $response = $this->post('/  ', $data);
//     // $response->assertSessionHasErrors('email');

//     $response->assertSessionHasErrors(['email']);

//     $data = [
//         'email' => 'admin@com',
//         'password' => '',
//     ];
//     $response = $this->post('/b2cstore', $data);


//     $response->assertSessionHasErrors(['password']);

// });

/**
 *  invalid email
 *
 *  @return bool True
 */
test('invalid_b2c_register_email', function () {
    $data = [
        'email' => 'testing.com',
        'password' => 'password',

    ];
    $response = $this->post('/b2cstore', $data);
    $response->assertSessionHasErrors('email');
});

/**
 *  check unique user for register
 *
 *  @return bool True
 */
test('unique_b2c_register_email', function () {
    $email = 'superadmin@mail.com';
    $name = explode('@', $email);
    $data = [
        'name' => $name[0],
        'email' => $email,
        'password' => 'password',
    ];
    $existingUser = UserEloquentModel::create($data);
    $response = $this->post('/b2cstore', [
        'name' => $existingUser->name,
        'email' => $existingUser->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
});


/**
 *  check empty email on login
 *
 *  @return  bool True
 */
test('bland login email or password', function () {
    $data = [
        'email' => '',
        'password' => 'password',
    ];
    $response = $this->post('login', $data);
    $response->assertSessionHasErrors('email');

    $data = [
        'email' => 'testing@testing.com',
        'password' => '',
    ];
    $response = $this->post('login', $data);
    $response->assertSessionHasErrors('password');
});

/**
 *  check invalid email address
 *
 *  @return bool True
 */
test('invalid_login_email', function () {
    $data = [
        'email' => 'testing.com',
        'password' => Hash::make('password'),
    ];
    $response = $this->post('login', $data);
    $response->assertSessionHasErrors('email');
});

/**
 *  check email and password mismatch
 *
 *   @return  bool True
 */
test('mismatch_login_password', function () {
    $data = [
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => 'password',
        'email_verified_at' => Carbon::now(),
    ];
    $existingUser = UserEloquentModel::create($data);

    $response = $this->post('login', [
        'email' => $existingUser->email,
        'password' => Hash::make('passwords'),
    ]);

    $response->assertInertia(function (AssertableInertia $page) {
        $props = $page->toArray();
        expect($props['props']['errorMessage'])->toBe('Invalid Login Credential');
    });
});

/***
 *  check user for valid login and redirect to home page
 *
 *
 */
test('match_login_password', function () {
    $data = [
        'name' => 'Admin',
        'email' => 'admin@testing.com',
        'password' => 'password',
        'email_verified_at' => Carbon::now(),

    ];
    $existingUser = UserEloquentModel::create($data);
    $response = $this->post('login', [
        'email' => $existingUser->email,
        'password' => 'password',
    ]);
    $response->assertRedirect('/home');
});
