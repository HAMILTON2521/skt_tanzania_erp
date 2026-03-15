@php
    $currentRoute = Route::currentRouteName();
@endphp

<aside class="admin-sidebar hidden w-full max-w-sm shrink-0 border-r xl:block xl:w-80">
    <div class="sticky top-0 flex h-screen flex-col">
        <div class="border-b border-white/10 px-6 py-6">
            <div class="skt-brand-lockup skt-brand-lockup--stacked">
                <img src="{{ asset('images/skt-logo.svg') }}" alt="SKT Tanzania logo" class="skt-brand-logo brand-image">
                <div>
                    <p class="admin-sidebar-brand-tagline text-xs font-semibold uppercase tracking-[0.35em]">{{ config('admin.brand.tagline') }}</p>
                    <h2 class="admin-sidebar-brand-title mt-2 text-2xl font-semibold">{{ config('admin.brand.name') }}</h2>
                </div>
            </div>
            <p class="mt-2 text-sm leading-6 text-slate-400">Operations, finance, HR and reporting.</p>
        </div>

        <nav class="flex-1 space-y-8 overflow-y-auto px-4 py-6">
            @foreach ($navigation as $section)
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $section['section'] }}</p>
                    <div class="mt-3 space-y-2">
                        @foreach ($section['items'] as $item)
                            @if (! empty($item['children']))
                                <div class="admin-sidebar-group rounded-2xl border p-3 shadow-lg shadow-slate-950/20">
                                    <div class="flex items-center gap-3 px-2 py-2">
                                        <span class="admin-sidebar-icon flex h-10 w-10 items-center justify-center rounded-xl text-sm font-semibold">{{ $item['initials'] }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="admin-sidebar-group-title font-medium">{{ $item['title'] }}</p>
                                            <p class="text-xs text-slate-400">{{ count($item['children']) }} {{ \Illuminate\Support\Str::plural('page', count($item['children'])) }}</p>
                                        </div>
                                        <span class="admin-sidebar-group-badge rounded-full border px-2 py-1 text-[10px] uppercase tracking-[0.2em]">Group</span>
                                    </div>
                                    <div class="mt-3 grid gap-1">
                                        @foreach ($item['children'] as $child)
                                            @php
                                                $childRoute = $child['route'] ?? 'admin.modules.show';
                                                $childParams = $child['route_params'] ?? ['module' => $child['slug']];
                                                $isActive = $currentRoute === $childRoute && request()->route('module') === ($childParams['module'] ?? null);
                                            @endphp
                                            <a
                                                href="{{ route($childRoute, $childParams) }}"
                                                class="admin-sidebar-group-link rounded-xl px-3 py-2 text-sm transition {{ $isActive ? 'admin-sidebar-group-link-active block' : 'block text-slate-300' }}"
                                            >
                                                <span class="block font-medium">{{ $child['title'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php
                                    $itemRoute = $item['route'] ?? 'admin.modules.show';
                                    $itemParams = $item['route_params'] ?? ['module' => $item['slug']];
                                    $isActive = $currentRoute === $itemRoute && (($itemRoute === 'admin.dashboard') || request()->route('module') === ($itemParams['module'] ?? null));
                                @endphp
                                <a
                                    href="{{ route($itemRoute, $itemParams) }}"
                                    class="admin-sidebar-link flex items-center gap-3 rounded-2xl border px-3 py-3 transition {{ $isActive ? 'admin-sidebar-link-active' : 'text-slate-300' }}"
                                >
                                    <span class="admin-sidebar-icon flex h-10 w-10 items-center justify-center rounded-xl text-sm font-semibold">{{ $item['initials'] }}</span>
                                    <div>
                                        <p class="font-medium">{{ $item['title'] }}</p>
                                        <p class="admin-sidebar-link-summary text-xs text-slate-400">{{ $item['summary'] }}</p>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>
    </div>
</aside>
