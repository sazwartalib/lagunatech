<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class GlobalSearch extends Component
{
    #[Url(except: '')]
    public string $q = '';

    /**
     * @return array<int, array{group: string, label: string, meta: string, url: string, icon: string}>
     */
    public function results(): array
    {
        $term = trim($this->q);

        if (mb_strlen($term) < 2) {
            return [];
        }

        $hits = [];

        if (auth()->user()->can('viewAny', Customer::class)) {
            foreach (Customer::query()->search($term)->limit(5)->get() as $customer) {
                $hits[] = [
                    'group' => 'Customers',
                    'label' => $customer->company_name,
                    'meta' => $customer->reference,
                    'url' => route('customers.show', $customer),
                    'icon' => '🏢',
                ];
            }
        }

        if (auth()->user()->can('viewAny', Project::class)) {
            foreach (Project::query()->search($term)->with('customer:id,company_name')->limit(6)->get() as $project) {
                $hits[] = [
                    'group' => 'Projects',
                    'label' => $project->name,
                    'meta' => $project->reference.' · '.$project->customer->company_name,
                    'url' => route('projects.show', $project),
                    'icon' => '📁',
                ];
            }

            foreach (Task::query()->where('title', 'like', "%{$term}%")->with('project:id,name')->limit(5)->get() as $task) {
                $hits[] = [
                    'group' => 'Tasks',
                    'label' => $task->title,
                    'meta' => $task->project->name,
                    'url' => route('projects.show', $task->project_id),
                    'icon' => '✓',
                ];
            }
        }

        return $hits;
    }

    public function render(): View
    {
        return view('livewire.global-search', [
            'results' => $this->results(),
        ]);
    }
}
