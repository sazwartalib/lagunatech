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
                <div>
                    <label for="lead-color-theme" class="mb-1.5 block text-xs font-medium text-white/50">Preferred color theme</label>
                    <input id="lead-color-theme" type="text" wire:model="color_theme" placeholder="e.g. Navy & gold, minimalist black/white"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('color_theme')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-slogan" class="mb-1.5 block text-xs font-medium text-white/50">Slogan / tagline <span class="text-white/25">(if any)</span></label>
                    <input id="lead-slogan" type="text" wire:model="slogan" placeholder="Your company's slogan"
                           class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50">
                    @error('slogan')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="lead-message" class="mb-1.5 block text-xs font-medium text-white/50">Describe your idea — how should it work? <span class="text-cyan-400">*</span></label>
                <textarea id="lead-message" wire:model="message" rows="4" placeholder="Walk us through the idea: the problem, the flow, who uses it, and what success looks like."
                          class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-white placeholder:text-white/25 transition focus:border-cyan-400/50 focus:bg-white/[0.05] focus:outline-none focus:ring-1 focus:ring-cyan-400/50"></textarea>
                @error('message')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="lead-logo" class="mb-1.5 block text-xs font-medium text-white/50">Company logo <span class="text-white/25">(if any)</span></label>
                    <label for="lead-logo"
                           class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-white/15 bg-white/[0.02] px-3.5 py-2.5 text-sm text-white/40 transition hover:border-cyan-400/40 hover:text-white/60">
                        <x-marketing.icon name="upload" class="size-4 shrink-0" />
                        <span class="truncate">{{ $logo ? $logo->getClientOriginalName() : 'Upload logo (image)' }}</span>
                    </label>
                    <input id="lead-logo" type="file" wire:model="logo" accept="image/*" class="hidden">
                    <div wire:loading wire:target="logo" class="mt-1 text-xs text-white/30">Uploading…</div>
                    @error('logo')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="lead-attachments" class="mb-1.5 block text-xs font-medium text-white/50">Reference docs / images <span class="text-white/25">(mockups, briefs — up to 5)</span></label>
                    <label for="lead-attachments"
                           class="flex cursor-pointer items-center gap-2 rounded-lg border border-dashed border-white/15 bg-white/[0.02] px-3.5 py-2.5 text-sm text-white/40 transition hover:border-cyan-400/40 hover:text-white/60">
                        <x-marketing.icon name="upload" class="size-4 shrink-0" />
                        <span class="truncate">{{ count($attachments) ? count($attachments).' file(s) selected' : 'Attach doc or image' }}</span>
                    </label>
                    <input id="lead-attachments" type="file" wire:model="attachments" multiple accept="image/*,.pdf,.doc,.docx" class="hidden">
                    <div wire:loading wire:target="attachments" class="mt-1 text-xs text-white/30">Uploading…</div>
                    @error('attachments')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    @error('attachments.*')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            @if (count($attachments))
                <ul class="flex flex-wrap gap-2">
                    @foreach ($attachments as $index => $file)
                        <li wire:key="attachment-{{ $index }}"
                            class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/[0.04] py-1 pl-3 pr-1.5 text-xs text-white/60">
                            <span class="max-w-[10rem] truncate">{{ $file->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeAttachment({{ $index }})" class="grid size-4 place-items-center rounded-full text-white/40 hover:bg-white/10 hover:text-white" aria-label="Remove file">
                                <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 6l12 12M18 6 6 18"/></svg>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif

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
