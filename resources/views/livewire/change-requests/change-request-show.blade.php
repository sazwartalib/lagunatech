@php
    use Illuminate\Support\Number;
    use App\Enums\ChangeRequestStatus;
    $cr = $changeRequest;
    $next = [
        ChangeRequestStatus::Pending->value => [['quoted', 'Mark as quoted', 'update']],
        ChangeRequestStatus::Quoted->value => [['waiting_approval', 'Send for approval', 'update']],
        ChangeRequestStatus::WaitingApproval->value => [['approved', 'Approve', 'decide'], ['rejected', 'Reject', 'decide']],
        ChangeRequestStatus::Approved->value => [['completed', 'Mark completed', 'update']],
    ][$cr->status->value] ?? [];
@endphp

<div class="mx-auto max-w-3xl">
    <x-ui.page-header :title="$cr->reference" :subtitle="$cr->title">
        <x-slot:breadcrumbs>
            <a href="{{ route('change-requests.index') }}" wire:navigate class="hover:text-slate-600">Change Requests</a>
            <span>/</span><span class="text-slate-500">{{ $cr->reference }}</span>
        </x-slot:breadcrumbs>
        <x-slot:actions>
            @can('update', $cr)
                <x-ui.button variant="secondary" href="{{ route('change-requests.edit', $cr) }}" icon="✎">Edit</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-5 flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
        <x-ui.badge :color="$cr->status->color()" size="md" dot>{{ $cr->status->label() }}</x-ui.badge>
        <div class="ml-auto flex flex-wrap gap-2">
            @foreach ($next as [$status, $label, $ability])
                @can($ability, $cr)
                    <x-ui.button size="sm" wire:click="changeStatus('{{ $status }}')"
                        variant="{{ $status === 'rejected' ? 'danger-ghost' : 'primary' }}">{{ $label }}</x-ui.button>
                @endcan
            @endforeach
            @can('convert', $cr)
                <x-ui.button size="sm" variant="secondary" wire:click="convertToTask"
                    wire:confirm="Create a project task from this change request?">Convert to Task →</x-ui.button>
            @endcan
        </div>
    </div>

    @if ($cr->task)
        <div class="mb-5 rounded-lg bg-green-50 px-4 py-2.5 text-sm text-green-800 ring-1 ring-inset ring-green-600/20">
            Converted to task: <span class="font-semibold">{{ $cr->task->title }}</span> ({{ $cr->task->status->label() }})
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-ui.card class="sm:col-span-2" title="Details">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Project</dt><dd><a href="{{ route('projects.show', $cr->project) }}" wire:navigate class="font-medium text-brand-700 hover:underline">{{ $cr->project->reference }} · {{ $cr->project->name }}</a></dd></div>
                <div><dt class="text-slate-400">Customer</dt><dd class="text-slate-800">{{ $cr->customer->company_name }}</dd></div>
                <div><dt class="text-slate-400">Description</dt><dd class="whitespace-pre-line text-slate-700">{{ $cr->description ?: '—' }}</dd></div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Commercials">
            <dl class="space-y-3 text-sm">
                <div><dt class="text-slate-400">Estimated cost</dt><dd class="text-lg font-semibold text-slate-900">{{ $cr->estimated_cost ? 'RM ' . Number::format((float) $cr->estimated_cost, 2) : '—' }}</dd></div>
                <div><dt class="text-slate-400">Additional days</dt><dd class="text-slate-800">{{ $cr->additional_days ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Requested by</dt><dd class="text-slate-800">{{ $cr->requester?->name ?? '—' }}</dd></div>
                <div><dt class="text-slate-400">Assigned to</dt><dd class="text-slate-800">{{ $cr->assignee?->name ?? '—' }}</dd></div>
                @if ($cr->decided_at)
                    <div><dt class="text-slate-400">Decided</dt><dd class="text-slate-800">{{ $cr->decided_at->format('d M Y') }}</dd></div>
                @endif
            </dl>
        </x-ui.card>
    </div>
</div>
