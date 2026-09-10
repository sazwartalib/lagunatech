<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Topbar notification bell — unread badge plus a dropdown of the latest items.
 */
class NotificationCenter extends Component
{
    public function markRead(string $id): void
    {
        auth()->user()->notifications()->whereKey($id)->update(['read_at' => now()]);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    #[On('notifications-updated')]
    public function refreshList(): void
    {
        // Re-render only.
    }

    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.notification-center', [
            'unreadCount' => $user->unreadNotifications()->count(),
            'items' => $user->notifications()->latest()->limit(8)->get(),
        ]);
    }
}
