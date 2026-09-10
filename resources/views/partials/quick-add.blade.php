@php
    $me = auth()->user();
    $quickLinks = array_values(array_filter([
        \Illuminate\Support\Facades\Route::has('customers.create') && $me->can('create', \App\Models\Customer::class)
            ? ['label' => 'New Customer', 'icon' => '🏢', 'href' => route('customers.create')] : null,
        \Illuminate\Support\Facades\Route::has('projects.create') && $me->can('create', \App\Models\Project::class)
            ? ['label' => 'New Project', 'icon' => '📁', 'href' => route('projects.create')] : null,
        \Illuminate\Support\Facades\Route::has('quotations.create') && $me->can('create', \App\Models\Quotation::class)
            ? ['label' => 'New Quotation', 'icon' => '📝', 'href' => route('quotations.create')] : null,
        \Illuminate\Support\Facades\Route::has('invoices.create') && $me->can('create', \App\Models\Invoice::class)
            ? ['label' => 'New Invoice', 'icon' => '🧾', 'href' => route('invoices.create')] : null,
    ]));
@endphp

@if (! empty($quickLinks))
    <div x-data="{ open: false }" class="relative" @click.outside="open = false">
        <button
            type="button"
            @click="open = !open"
            class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-2.5 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-brand-700"
        >
            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            <span class="hidden sm:inline">Create</span>
        </button>

        <div
            x-show="open" x-cloak x-transition.origin.top.right
            class="absolute right-0 mt-2 w-52 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg"
        >
            @foreach ($quickLinks as $link)
                <a href="{{ $link['href'] }}" wire:navigate
                   class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm text-slate-700 hover:bg-slate-100">
                    <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                </a>
            @endforeach
        </div>
    </div>
@endif
