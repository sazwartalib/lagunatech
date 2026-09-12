<?php

use App\Enums\Role;
use App\Livewire\Drawings\DrawingIndex;
use App\Livewire\Drawings\DrawingManager;
use App\Models\Drawing;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('a drawing can be created against a project', function () {
    actingAsRole(Role::Developer);
    $project = Project::factory()->create();

    Livewire::test(DrawingManager::class, ['drawable' => $project])
        ->set('newTitle', 'Homepage Wireframe')
        ->call('create')
        ->assertHasNoErrors();

    $drawing = $project->drawings()->first();

    expect($drawing)->not->toBeNull()
        ->and($drawing->title)->toBe('Homepage Wireframe')
        ->and($drawing->status->value)->toBe('draft')
        ->and($drawing->drawable_type)->toBe(Project::class);
});

test('a drawing can be created standalone, without a project', function () {
    actingAsRole(Role::Developer);

    Livewire::test(DrawingIndex::class)
        ->set('newTitle', 'Quick Client Sketch')
        ->call('create')
        ->assertHasNoErrors();

    $drawing = Drawing::where('title', 'Quick Client Sketch')->first();

    expect($drawing)->not->toBeNull()
        ->and($drawing->drawable_type)->toBeNull()
        ->and($drawing->drawable_id)->toBeNull();
});

test('a standalone drawing is viewable and editable like any other', function () {
    actingAsRole(Role::Developer);
    $drawing = Drawing::factory()->create(['drawable_type' => null, 'drawable_id' => null]);

    $this->get(route('drawings.show', $drawing))->assertOk()->assertSee($drawing->title);
    $this->get(route('drawings.present', $drawing))->assertOk();

    $this->patch(route('drawings.update', $drawing), ['canvas_data' => ['objects' => []]])
        ->assertOk();
});

test('a support user without create-drawings permission cannot create one', function () {
    $this->actingAs(userWithRole(Role::Finance));
    $project = Project::factory()->create();

    Livewire::test(DrawingManager::class, ['drawable' => $project])
        ->set('newTitle', 'Blocked drawing')
        ->call('create')
        ->assertForbidden();
});

test('deleting a drawing removes its stored files', function () {
    Storage::fake('public');
    actingAsRole(Role::Admin);
    $project = Project::factory()->create();
    $drawing = Drawing::factory()->forDrawable($project)->create();

    Storage::disk('public')->put("drawings/{$drawing->id}/thumbnail.png", 'fake-bytes');

    Livewire::test(DrawingManager::class, ['drawable' => $project])
        ->call('deleteDrawing', $drawing->id);

    Storage::disk('public')->assertMissing("drawings/{$drawing->id}/thumbnail.png");
    expect($project->drawings()->count())->toBe(0);
});

test('the autosave endpoint persists canvas data for an authorized user', function () {
    $user = actingAsRole(Role::Developer);
    $drawing = Drawing::factory()->create();

    $canvasData = ['version' => '6.0.0', 'objects' => [['type' => 'rect']]];

    $this->patch(route('drawings.update', $drawing), ['canvas_data' => $canvasData])
        ->assertOk()
        ->assertJsonStructure(['saved_at']);

    $drawing->refresh();
    expect($drawing->canvas_data)->toBe($canvasData)
        ->and($drawing->updated_by)->toBe($user->id);
});

test('the autosave endpoint is forbidden without edit-drawings permission', function () {
    $this->actingAs(userWithRole(Role::Finance));
    $drawing = Drawing::factory()->create();

    $this->patch(route('drawings.update', $drawing), ['canvas_data' => ['objects' => []]])
        ->assertForbidden();
});

test('an image can be uploaded to a drawing', function () {
    Storage::fake('public');
    actingAsRole(Role::Designer);
    $drawing = Drawing::factory()->create();

    $response = $this->post(route('drawings.images.store', $drawing), [
        'image' => UploadedFile::fake()->image('screenshot.png', 800, 600),
    ]);

    $response->assertOk()->assertJsonStructure(['url']);
    Storage::disk('public')->assertExists("drawings/{$drawing->id}/images/".basename($response->json('url')));
});

test('image upload rejects a non-image file', function () {
    actingAsRole(Role::Designer);
    $drawing = Drawing::factory()->create();

    $this->post(route('drawings.images.store', $drawing), [
        'image' => UploadedFile::fake()->create('not-an-image.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('image');
});

test('exporting requires the export-drawings permission', function () {
    $this->actingAs(userWithRole(Role::Finance));
    $drawing = Drawing::factory()->create();

    $this->post(route('drawings.export.pdf', $drawing), [
        'image' => 'data:image/png;base64,'.base64_encode('fake'),
    ])->assertForbidden();
});

test('the drawings index lists drawings the user can view', function () {
    actingAsRole(Role::ProjectManager);
    Drawing::factory()->count(3)->create();

    $this->get(route('drawings.index'))->assertOk();
});

test('the editor page renders for an authorized viewer', function () {
    actingAsRole(Role::Developer);
    $drawing = Drawing::factory()->create();

    $this->get(route('drawings.show', $drawing))
        ->assertOk()
        ->assertSee($drawing->title);
});

test('a user without view-drawings permission cannot open the editor', function () {
    $this->actingAs(userWithRole(Role::Finance));
    $drawing = Drawing::factory()->create();

    $this->get(route('drawings.show', $drawing))->assertForbidden();
});

test('presentation mode renders for an authorized viewer', function () {
    actingAsRole(Role::Developer);
    $drawing = Drawing::factory()->create();

    $this->get(route('drawings.present', $drawing))
        ->assertOk()
        ->assertSee($drawing->title);
});
