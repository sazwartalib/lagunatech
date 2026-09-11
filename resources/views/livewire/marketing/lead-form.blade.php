<div class="relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.03] p-6 sm:p-8">
    <div class="pointer-events-none absolute -right-16 -top-16 size-56 rounded-full bg-cyan-500/10 blur-3xl"></div>

    @if ($submitted)
        <div class="relative flex flex-col items-center py-10 text-center">
            <div class="grid size-14 place-items-center rounded-full bg-cyan-400/10 text-cyan-300">
                <x-marketing.icon name="check" class="size-6" />
            </div>
            <h3 class="mt-5 text-xl font-semibold text-white">Thank you — your enquiry is in.</h3>
            <p class="mt-2 max-w-xs text-sm text-white/50">Our team will get back to you within one business day.</p>
            <button type="button" wire:click="$set('submitted', false)"
                    class="mt-6 text-sm font-medium text-cyan-300 transition hover:text-cyan-200">Send another enquiry</button>
        </div>
    @else
        <form wire:submit="submit" class="relative space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="lead-name" class="mb-1.5 block text-xs font-medium text-white/50">Name <span class="text-cyan-400">*</span></label>
                    <input id="lead-name" type="text" wire:model="name"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-company" class="mb-1.5 block text-xs font-medium text-white/50">Company</label>
                    <input id="lead-company" type="text" wire:model="company"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('company')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-email" class="mb-1.5 block text-xs font-medium text-white/50">Email <span class="text-cyan-400">*</span></label>
                    <input id="lead-email" type="email" wire:model="email"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-phone" class="mb-1.5 block text-xs font-medium text-white/50">Phone / WhatsApp <span class="text-cyan-400">*</span></label>
                    <input id="lead-phone" type="text" wire:model="phone" placeholder="012-3456789"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('phone')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-project-type" class="mb-1.5 block text-xs font-medium text-white/50">Project type</label>
                    <select id="lead-project-type" wire:model="project_type"
                            class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                        <option value="" class="bg-[#0B0F16]">Select…</option>
                        @foreach ($this->projectTypes() as $type)<option value="{{ $type }}" class="bg-[#0B0F16]">{{ $type }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label for="lead-budget" class="mb-1.5 block text-xs font-medium text-white/50">Estimated budget</label>
                    <select id="lead-budget" wire:model="budget_range"
                            class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                        <option value="" class="bg-[#0B0F16]">Select…</option>
                        @foreach ($this->budgetRanges() as $range)<option value="{{ $range }}" class="bg-[#0B0F16]">{{ $range }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="lead-message" class="mb-1.5 block text-xs font-medium text-white/50">Tell us about your project <span class="text-cyan-400">*</span></label>
                <textarea id="lead-message" wire:model="message" rows="4" placeholder="What problem are you trying to solve? What does success look like?"
                          class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50"></textarea>
                @error('message')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                    class="group inline-flex w-full items-center justify-center gap-2 rounded-lg bg-white px-5 py-3.5 text-sm font-semibold text-[#05070A] transition hover:bg-cyan-300 disabled:opacity-60 sm:w-auto"
                    wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                    Send Project Inquiry
                    <svg class="size-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
            <p class="text-xs text-white/30">We only use your details to respond to this enquiry.</p>
        </form>
    @endif
</div>
