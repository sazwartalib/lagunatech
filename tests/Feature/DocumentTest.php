<?php

use App\Enums\Role;
use App\Livewire\Documents\DocumentManager;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('a document can be uploaded against a project', function () {
    Storage::fake('local');
    actingAsRole(Role::Developer);
    $project = Project::factory()->create();

    Livewire::test(DocumentManager::class, ['project' => $project])
        ->set('file', UploadedFile::fake()->create('spec.pdf', 200, 'application/pdf'))
        ->set('category', 'requirements')
        ->set('isInternal', false)
        ->call('upload')
        ->assertHasNoErrors();

    $document = $project->documents()->first();

    expect($document)->not->toBeNull()
        ->and($document->is_internal)->toBeFalse()
        ->and($document->category->value)->toBe('requirements');
    Storage::disk('local')->assertExists($document->path);
});

test('deleting a document removes its stored file', function () {
    Storage::fake('local');
    actingAsRole(Role::Admin);
    $project = Project::factory()->create();

    $component = Livewire::test(DocumentManager::class, ['project' => $project])
        ->set('file', UploadedFile::fake()->create('notes.pdf', 100))
        ->set('category', 'other')
        ->call('upload');

    $document = $project->documents()->first();
    $path = $document->path;

    $component->call('deleteDocument', $document->id);

    Storage::disk('local')->assertMissing($path);
    expect($project->documents()->count())->toBe(0);
});

test('a finance user without the documents permission cannot upload', function () {
    Storage::fake('local');
    $this->actingAs(userWithRole(Role::Finance));
    $project = Project::factory()->create();

    Livewire::test(DocumentManager::class, ['project' => $project])
        ->set('file', UploadedFile::fake()->create('x.pdf', 10))
        ->set('category', 'other')
        ->call('upload')
        ->assertForbidden();
});

test('the customer-visible scope excludes internal documents', function () {
    $project = Project::factory()->create();
    Document::factory()->for($project)->create(['is_internal' => true]);
    Document::factory()->for($project)->shared()->create();

    expect($project->documents()->customerVisible()->count())->toBe(1);
});
