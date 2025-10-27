<?php

use App\Models\User;

test('test-profile route creates or returns fisherman profile', function () {
    // Ensure there is at least one user
    $user = User::factory()->create();

    // Call the route
    $response = $this->get('/test-profile');

    $response->assertOk();
    $response->assertJsonFragment([
        'vessel_name' => 'Blue Dolphin',
        'vessel_type' => 'bangka',
    ]);
});
