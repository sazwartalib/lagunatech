<?php

namespace App\Livewire\Marketing;

use App\Actions\Leads\CaptureLead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class LeadForm extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $company = '';

    public string $email = '';

    public string $phone = '';

    public string $project_type = '';

    public string $budget_range = '';

    public string $color_theme = '';

    public string $slogan = '';

    public string $message = '';

    /** Company logo, if they have one. */
    public $logo = null;

    /** Reference docs/images explaining the idea (mockups, sketches, briefs). */
    public array $attachments = [];

    /** Honeypot — real users never fill this. */
    public string $website = '';

    public bool $submitted = false;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'company' => 'nullable|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:40',
            'project_type' => 'nullable|string|max:80',
            'budget_range' => 'nullable|string|max:80',
            'color_theme' => 'nullable|string|max:80',
            'slogan' => 'nullable|string|max:150',
            'message' => 'required|string|max:3000',
            'logo' => 'nullable|image|max:5120',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx',
        ];
    }

    /**
     * @return list<string>
     */
    public function projectTypes(): array
    {
        return ['Web Application', 'Mobile Application', 'Custom Software System', 'Website', 'E-commerce / Online Store', 'Technical Service', 'Not sure yet'];
    }

    /**
     * @return list<string>
     */
    public function budgetRanges(): array
    {
        return ['Below RM 10,000', 'RM 10,000 – 30,000', 'RM 30,000 – 60,000', 'RM 60,000+', 'Not sure yet'];
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
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

        $capture->handle($data, request()->ip(), $this->logo ?: null, $this->attachments);

        $this->reset(['name', 'company', 'email', 'phone', 'project_type', 'budget_range', 'color_theme', 'slogan', 'message', 'logo', 'attachments']);
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('livewire.marketing.lead-form');
    }
}
