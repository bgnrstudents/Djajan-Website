@props([
    'badge',
    'title',
    'subtitle',
])

<section {{ $attributes->merge(['class' => 'admin-page-hero relative overflow-hidden rounded-2xl px-5 py-5 sm:px-7 sm:py-6']) }}>
    <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-4">
            @isset($icon)
                <div class="admin-page-hero-icon hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl sm:inline-flex" aria-hidden="true">
                    {{ $icon }}
                </div>
            @endisset
            <div class="min-w-0">
                <div class="admin-page-hero-badge inline-flex items-center gap-2 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-djajan-primary"></span>
                    <span>{{ $badge }}</span>
                </div>
                <h1 class="mt-2.5 text-2xl font-extrabold tracking-tight text-djajan-neutral font-heading sm:text-[28px]">{{ $title }}</h1>
                <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ $subtitle }}</p>
            </div>
        </div>

        @isset($right)
            <div class="shrink-0 self-start sm:self-center">
                {{ $right }}
            </div>
        @endisset
    </div>
</section>
