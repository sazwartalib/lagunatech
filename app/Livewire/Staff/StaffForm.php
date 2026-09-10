<?php

namespace App\Livewire\Staff;

use App\Actions\Staff\UpsertStaff;
use App\Enums\Role as RoleEnum;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class StaffForm extends Component
{
    public ?User $staff = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('nullable|string|max:100')]
    public string $position = '';

    #[Validate('nullable|string|max:100')]
    public string $department = '';

    public bool $is_active = true;

    /** @var list<string> */
    #[Validate(['roles' => 'array', 'roles.*' => 'string'])]
    public array $roles = [];

    public function mount(?User $staff = null): void
    {
        if ($staff?->exists) {
            $this->authorize('update', $staff);
            $this->staff = $staff;
            $this->name = $staff->name;
            $this->email = $staff->email;
            $this->phone = (string) $staff->phone;
            $this->position = (string) $staff->position;
            $this->department = (string) $staff->department;
            $this->is_active = $staff->is_active;
            $this->roles = $staff->getRoleNames()->all();

            return;
        }

        $this->authorize('create', User::class);
    }

    public function save(UpsertStaff $upsert): void
    {
        $this->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->staff?->id)],
            'roles' => ['array', 'min:1'],
            'roles.*' => [Rule::in(RoleEnum::values())],
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'position' => $this->position ?: null,
            'department' => $this->department ?: null,
            'is_active' => $this->is_active,
        ];

        $user = $upsert->handle($data, $this->roles, $this->staff);

        session()->flash('status', $this->staff ? 'Staff member updated.' : "{$user->name} added. They can set a password via “forgot password”.");
        $this->redirectRoute('staff.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.staff.staff-form', [
            'title' => $this->staff ? 'Edit '.$this->staff->name : 'Add staff member',
            'allRoles' => RoleEnum::cases(),
        ]);
    }
}
