<?php

namespace App\Livewire\Staff;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Staff')]
class StaffIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'true')]
    public bool $activeOnly = true;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'activeOnly'], true)) {
            $this->resetPage();
        }
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->is_active) {
            $this->authorize('deactivate', $user);
        } else {
            $this->authorize('update', $user);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->dispatch('toast', message: $user->is_active ? 'Account activated.' : 'Account deactivated.', type: 'info');
    }

    public function render(): View
    {
        $staff = User::query()
            ->with('roles:id,name')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($inner) => $inner
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->activeOnly, fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.staff.staff-index', ['staff' => $staff]);
    }
}
