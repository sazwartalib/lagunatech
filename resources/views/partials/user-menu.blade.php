@php($me = auth()->user())

<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-lg p-1 hover:bg-slate-100">
        <x-ui.avatar :name="$me->name" size="sm" />
        <span class="hidden text-sm font-medium text-slate-700 sm:inline">{{ \Illuminate\Support\Str::of($me->name)->explode(' ')->first() }}</span>
        <svg class="hidden size-4 text-slate-400 sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
    </button>

    <div x-show="open" x-cloak x-transition.origin.top.right
         class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
        <div class="border-b border-slate-100 px-3 py-2">
            <p class="truncate text-sm font-medium text-slate-900">{{ $me->name }}</p>
            <p class="truncate text-xs text-slate-500">{{ $me->email }}</p>
            @if ($me->position)
                <p class="mt-0.5 text-xs text-slate-400">{{ $me->position }}</p>
            @endif
        </div>
        <div class="py-1">
            <span class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-400">
                Profile <span class="ml-auto text-[10px] font-semibold">Soon</span>
            </span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-1">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-100">
                Sign out
            </button>
        </form>
    </div>
</div>
