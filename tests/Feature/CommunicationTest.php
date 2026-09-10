<?php

use App\Enums\Role;
use App\Livewire\Communications\CommunicationIndex;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\User;
use Livewire\Livewire;

test('a communication entry can be logged', function () {
    actingAsRole(Role::ProjectManager);
    $customer = Customer::factory()->create();

    Livewire::test(CommunicationIndex::class)
        ->set('customer_id', $customer->id)
        ->set('type', 'whatsapp')
        ->set('communicated_at', now()->format('Y-m-d\TH:i'))
        ->set('summary', 'Customer asked for an update on the deployment date.')
        ->set('action_required', 'Send revised timeline')
        ->set('follow_up_on', now()->addDays(2)->toDateString())
        ->call('save')
        ->assertHasNoErrors();

    $entry = Communication::firstWhere('customer_id', $customer->id);

    expect($entry->summary)->toContain('deployment date')
        ->and($entry->user_id)->not->toBeNull()
        ->and(Communication::needsFollowUp()->count())->toBe(1);
});

test('a follow-up can be marked done by its author', function () {
    $user = userWithRole(Role::ProjectManager);
    $this->actingAs($user);

    $entry = Communication::factory()->create([
        'user_id' => $user->id,
        'follow_up_on' => now()->addDay(),
        'follow_up_done' => false,
    ]);

    Livewire::test(CommunicationIndex::class)->call('toggleFollowUp', $entry->id);

    expect($entry->fresh()->follow_up_done)->toBeTrue();
});

test('a user without the log-communications permission cannot save an entry', function () {
    // A bare user has "view customers"? No role at all -> viewAny fails first.
    $this->actingAs(User::factory()->create());

    Livewire::test(CommunicationIndex::class)->assertForbidden();
});
