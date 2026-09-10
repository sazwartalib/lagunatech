<?php

use App\Enums\Permission;
use App\Enums\Role;
use App\Livewire\System\RoleMatrix;
use App\Livewire\System\SettingsForm;
use App\Support\Settings;
use Livewire\Livewire;
use Spatie\Permission\Models\Role as SpatieRole;

test('the role matrix updates a role\'s permissions', function () {
    actingAsRole(Role::SuperAdmin);

    Livewire::test(RoleMatrix::class)
        ->set('grid.'.Role::Support->value.'.'.Permission::ManageInvoices->value, true)
        ->call('save');

    expect(SpatieRole::findByName(Role::Support->value, 'web')->hasPermissionTo(Permission::ManageInvoices->value))->toBeTrue();
});

test('the role matrix never strips the super admin', function () {
    actingAsRole(Role::SuperAdmin);

    Livewire::test(RoleMatrix::class)
        ->set('grid.'.Role::SuperAdmin->value.'.'.Permission::ManageStaff->value, false)
        ->call('save');

    expect(SpatieRole::findByName(Role::SuperAdmin->value, 'web')->hasPermissionTo(Permission::ManageStaff->value))->toBeTrue();
});

test('a project manager cannot open the role matrix', function () {
    $this->actingAs(userWithRole(Role::ProjectManager));

    $this->get(route('system.roles'))->assertForbidden();
});

test('settings can be saved and are read back', function () {
    actingAsRole(Role::Admin);

    Livewire::test(SettingsForm::class)
        ->set('companyName', 'Laguna Tech Global')
        ->set('defaultTaxRate', 6)
        ->call('save')
        ->assertHasNoErrors();

    $settings = app(Settings::class);
    $settings->flush();

    expect($settings->get('company.name'))->toBe('Laguna Tech Global')
        ->and((int) $settings->get('finance.default_tax_rate'))->toBe(6);
});

test('a developer cannot open settings', function () {
    $this->actingAs(userWithRole(Role::Developer));

    $this->get(route('system.settings'))->assertForbidden();
});

test('the activity log viewer renders for an admin', function () {
    actingAsRole(Role::Admin);

    $this->get(route('system.activity'))->assertOk();
});
