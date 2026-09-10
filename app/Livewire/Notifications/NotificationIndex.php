<?php

namespace App\Livewire\Notifications;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Notifications')]
class NotificationIndex extends Component
{
    use WithPagination;

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('toast', message: 'All notifications marked as read.');
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->whereKey($id)->update(['read_at' => now()]);
    }

    public function render(): View
    {
        return view('livewire.notifications.notification-index', [
            'items' => auth()->user()->notifications()->latest()->paginate(20),
            'unread' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
