<?php

use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates updates and deletes labels for authenticated user', function () {
    $user = User::factory()->create();

    $create = $this->actingAs($user)
        ->from('/')
        ->post('/labels', [
            'name' => 'Feature',
            'color' => '#16a34a',
        ]);

    $create->assertRedirect('/');

    $label = Label::query()->firstOrFail();

    expect($label->name)->toBe('Feature');
    expect($label->color)->toBe('#16a34a');

    $update = $this->actingAs($user)
        ->from('/')
        ->patch('/labels/'.$label->id, [
            'name' => 'Refactor',
            'color' => '#f97316',
        ]);

    $update->assertRedirect('/');

    $label->refresh();

    expect($label->name)->toBe('Refactor');
    expect($label->color)->toBe('#f97316');

    $delete = $this->actingAs($user)
        ->from('/')
        ->delete('/labels/'.$label->id);

    $delete->assertRedirect('/');
    expect(Label::query()->count())->toBe(0);
});

it('does not allow editing labels owned by another user', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $label = Label::query()->create([
        'user_id' => $owner->id,
        'name' => 'Bug Fix',
        'color' => '#dc2626',
    ]);

    $response = $this->actingAs($otherUser)
        ->patch('/labels/'.$label->id, [
            'name' => 'Should Not Work',
            'color' => '#000000',
        ]);

    $response->assertNotFound();
});
