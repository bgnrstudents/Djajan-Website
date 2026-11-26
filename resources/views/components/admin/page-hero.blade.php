@props([
    'badge',
    'title',
    'subtitle',
])

<section {{ $attributes->merge(['class' => 'admin-page-hero relative overflow-hidden rounded-2xl px-5 py-4 sm:px-6 sm:py-5']) }}>
    <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4 min-w-0">
            @isset($icon)
                <div class="admin-page-hero-icon hidden h-13 w-13 shrink-0 items-center justify-center rounded-2xl sm:inline-flex" aria-hidden="true">
                    {{ $icon }}
                </div>
            @endisset
            <div class="min-w-0">
                <div class="admin-page-hero-badge inline-flex items-center gap-1.5 rounded-md px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-djajan-primary"></span>
                    <span>{{ $badge }}</span>
                </div>
                <h1 class="mt-1.5 text-xl font-extrabold tracking-tight text-djajan-neutral font-heading sm:text-[26px] sm:leading-tight">{{ $title }}</h1>
                <p class="mt-0.5 text-sm text-gray-500 leading-snug">{{ $subtitle }}</p>
            </div>
        </div>

        @isset($right)
            <div class="shrink-0 self-start sm:self-center">
                {{ $right }}
            </div>
        @endisset
    </div>
</section>
