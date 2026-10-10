<?php

use App\Models\User;

test('appearance settings page is displayed for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appearance.edit'));

    $response->assertOk();
    $response->assertSee('Appearance');
});
