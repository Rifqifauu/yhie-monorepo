<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index returns all settings in custom format', function () {
    Setting::create(['key' => 'site_name', 'value' => 'YHIE']);
    Setting::create(['key' => 'wa_number', 'value' => '12345']);

    $response = $this->getJson('/api/settings');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Settings fetched successfully.',
            'data' => [
                [
                    'key' => 'site_name',
                    'value' => 'YHIE',
                ],
                [
                    'key' => 'wa_number',
                    'value' => '12345',
                ]
            ]
        ]);
});

test('store returns 422 if validation fails with error message', function () {
    Setting::create(['key' => 'duplicate_key', 'value' => 'value']);

    $response = $this->postJson('/api/settings', [
        'key' => 'duplicate_key',
        'value' => 'some value'
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Validation failed: The key has already been taken.',
            'errors' => [
                'key' => [
                    'The key has already been taken.'
                ]
            ]
        ]);
});

test('show returns single setting', function () {
    $setting = Setting::create(['key' => 'site_name', 'value' => 'YHIE']);

    $response = $this->getJson('/api/settings/site_name');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Setting fetched successfully.',
            'data' => [
                'key' => 'site_name',
                'value' => 'YHIE',
            ]
        ]);
});

test('show returns 404 if setting not found', function () {
    $response = $this->getJson('/api/settings/non_existent');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Setting not found.',
            'data' => null
        ]);
});

test('store creates a new setting', function () {
    $response = $this->postJson('/api/settings', [
        'key' => 'email',
        'value' => 'info@example.com'
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Setting created successfully.',
            'data' => [
                'key' => 'email',
                'value' => 'info@example.com',
            ]
        ]);

    $this->assertDatabaseHas('settings', [
        'key' => 'email',
        'value' => 'info@example.com'
    ]);
});

test('update modifies/creates setting key-value', function () {
    $response = $this->putJson('/api/settings/site_name', [
        'value' => 'New YHIE Name'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Setting updated successfully.',
            'data' => [
                'key' => 'site_name',
                'value' => 'New YHIE Name'
            ]
        ]);

    $this->assertDatabaseHas('settings', [
        'key' => 'site_name',
        'value' => 'New YHIE Name'
    ]);
});

test('destroy deletes setting', function () {
    Setting::create(['key' => 'temp_key', 'value' => 'temp_val']);

    $response = $this->deleteJson('/api/settings/temp_key');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Setting deleted successfully.',
            'data' => null
        ]);

    $this->assertDatabaseMissing('settings', [
        'key' => 'temp_key'
    ]);
});
