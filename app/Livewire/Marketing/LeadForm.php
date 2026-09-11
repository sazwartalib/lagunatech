<?php

namespace App\Livewire\Marketing;

use App\Actions\Leads\CaptureLead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

class LeadForm extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:150')]
    public string $company = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('required|string|max:40')]
    public string $phone = '';

    #[Validate('nullable|string|max:80')]
    public string $project_type = '';

    #[Validate('nullable|string|max:80')]
    public string $budget_range = '';

    #[Validate('required|string|max:3000')]
    public string $message = '';

    /** Honeypot — real users never fill this. */
    public string $website = '';

    public bool $submitted = false;

    /**
     * @return list<string>
     */
    public function projectTypes(): array
    {
        return ['Web Application', 'Mobile Application', 'Custom Software System', 'Website', 'Technical Service', 'Not sure yet'];
    }

    /**
     * @return list<string>
     */
    public function budgetRanges(): array
    {
        return ['Below RM 10,000', 'RM 10,000 – 30,000', 'RM 30,000 – 60,000', 'RM 60,000+', 'Not sure yet'];
    }

    public function submit(CaptureLead $capture): void
    {
        // Silently accept bot submissions but do nothing.
        if ($this->website !== '') {
            $this->submitted = true;

            return;
        }

        $key = 'lead-form:'.Str::lower(request()->ip());

        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages([
                'message' => 'You have sent a few enquiries already. Please email us directly and we will get back to you.',
            ]);
        }

        $data = $this->validate();
        RateLimiter::hit($key, 3600);

        $capture->handle($data, request()->ip());

        $this->reset(['name', 'company', 'email', 'phone', 'project_type', 'budget_range', 'message']);
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.marketing.lead-form');
    }
}
