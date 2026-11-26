<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Document')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 antialiased">
    <div
        x-data="{
            open: false,
            sections: {
                managements: {{ request()->routeIs('admin.reports.*', 'inventories.*') ? 'true' : 'true' }},
                weather: {{ request()->routeIs('map') ? 'true' : 'false' }},
                roles: false,
            },
            search: '',
            active: '{{ request()->routeIs('admin.reports.*') ? 'report' : (request()->routeIs('inventories.*') ? 'tools' : (request()->routeIs('map') ? 'map' : 'overview')) }}',
        }"
        @sidebar-toggle.window="open = !open"
        @keydown.escape.window="open = false"
        class="relative min-h-screen lg:flex bg-white dark:bg-zinc-900"
    >
        <!-- Mobile top bar -->
        <header class="lg:hidden sticky top-0 z-40 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between px-4 h-12">
                <div class="flex items-center gap-2">
                    <svg class="text-zinc-900 dark:text-white size-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">SiagaKampus</span>
                </div>

                <button
                    type="button"
                    @click="$dispatch('sidebar-toggle')"
                    :aria-expanded="open"
                    aria-label="Open navigation"
                    class="flex items-center justify-center size-7 p-0.5 text-xs font-medium rounded-md text-zinc-600 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-200/70 dark:hover:bg-zinc-700 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path x-show="!open" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="open" x-cloak d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Overlay for mobile -->
        <div
            class="fixed inset-0 z-40 bg-black/40 dark:bg-black/60 transition-opacity duration-200 lg:hidden"
            x-show="open"
            x-cloak
            x-transition.opacity
            @click="open = false"
            aria-hidden="true"
        ></div>

        <!-- Sidebar panel -->
        <aside
            class="fixed z-50 inset-y-0 left-0 w-72 lg:w-80 lg:static lg:translate-x-0 transform transition-transform duration-200 -translate-x-full bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-800 flex flex-col shrink-0"
            :class="open ? 'translate-x-0' : ''"
        >
            <!-- Header -->
            <div class="h-14 shrink-0 flex items-center justify-between px-4 lg:px-5 border-b border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2.5">
                    <svg fill="none" height="36" viewBox="0 0 40 48" width="36" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                        <g clip-rule="evenodd" fill-rule="evenodd">
                            <path d="m4.29111 28.6025c.90712.9019.9071 2.3641-.00003 3.266l-.04646.0462c-.90713.9019-2.37788.9018-3.284994-.0001-.9071177-.9019-.9071038-2.3641.000031-3.266l.046453-.0462c.90714-.9019 2.37788-.9018 3.285.0001z" fill="#a5b4fc"/>
                            <path d="m15.2112 29.298c.9055.9035.9028 2.3658-.006 3.266l-6.43424 6.3741c-.90876.9002-2.3795.8976-3.28499-.0059-.90548-.9035-.90282-2.3658.00594-3.266l6.43429-6.3741c.9088-.9002 2.3795-.8976 3.285.0059z" fill="#6366f1"/>
                            <path d="m23.9431 40.2706c.7116-1.0613 2.1538-1.348 3.2212-.6405l.1394.0923c1.0674.7075 1.3558 2.1414.6442 3.2026-.7116 1.0613-2.1538 1.3481-3.2212.6406l-.1394-.0924c-1.0674-.7075-1.3558-2.1414-.6442-3.2026z" fill="#a5b4fc"/>
                            <path d="m39.2728 28.4638c.9072.9019.9072 2.3641 0 3.266l-3.159 3.1408c-.9072.9019-2.3779.9019-3.285 0s-.9071-2.3641 0-3.266l3.1591-3.1408c.9071-.9019 2.3778-.9019 3.2849 0z" fill="#6366f1"/>
                            <g fill="#4f46e5">
                                <path d="m39.3197 16.8473c.9071.9019.9071 2.3642 0 3.266l-23.3446 23.2098c-.9071.9019-2.3779.9019-3.285 0s-.9071-2.3641 0-3.266l23.3446-23.2098c.9071-.9019 2.3778-.9019 3.285 0z"/>
                                <path d="m34.905 9.71133c.9072.90187.9072 2.36417 0 3.26607l-13.426 13.3485c-.9071.9019-2.3779.9019-3.285 0s-.9071-2.3642 0-3.2661l13.4261-13.34847c.9071-.90189 2.3778-.90189 3.2849 0z"/>
                                <path d="m27.8187 5.18362c.9077.90128.9087 2.36353.0022 3.26603l-17.2588 17.18215c-.90647.9025-2.37721.9035-3.28495.0022-.90773-.9013-.90872-2.3635-.00221-3.266l17.25876-17.18218c.9065-.9025 2.3772-.90348 3.285-.0022z"/>
                            </g>
                            <path d="m10.7276 10.6841c.9056.9035.9031 2.3657-.0056 3.266l-6.75949 6.6974c-.90869.9003-2.37943.8978-3.28499-.0056-.905563-.9035-.903032-2.3657.005654-3.2661l6.759476-6.6973c.90868-.90034 2.37942-.89783 3.28495.0056z" fill="#6366f1"/>
                            <path d="m14.8678 4.00024c1.2828 0 2.3228 1.03397 2.3228 2.30944v.23094c0 1.27546-1.04 2.30943-2.3228 2.30943-1.2829 0-2.3229-1.03397-2.3229-2.30943v-.23094c0-1.27547 1.04-2.30944 2.3229-2.30944z" fill="#a5b4fc"/>
                        </g>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 leading-tight">SiagaKampus</p>
                    </div>
                </div>

                <button
                    type="button"
                    class="lg:hidden p-1.5 rounded-md border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800"
                    @click="open = false"
                    aria-label="Close navigation"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                        <path d="M18 6l-12 12M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Search -->
            <div class="p-3 lg:p-4">
                <label class="relative block">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-zinc-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Find anything…"
                        class="block w-full h-9 pl-9 pr-3 py-2 text-xs sm:text-sm bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-lg border border-transparent ring-1 ring-zinc-200 dark:ring-zinc-700 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 transition-colors"
                    />
                </label>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-2 lg:px-3 pb-6 overflow-y-auto">
                <ul class="space-y-1">
                    <li>
                        <a
                            href="{{ url('/') }}"
                            @click="active = 'overview'"
                            class="w-full h-9 px-3 rounded-md flex items-center gap-2 text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                            :class="active === 'overview' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Managements collapsible -->
                    <li class="pt-2 mt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <button
                            type="button"
                            class="w-full h-9 px-3 rounded-md flex items-center justify-between text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5"
                            @click="sections.managements = !sections.managements"
                            :aria-expanded="sections.managements"
                        >
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="2" />
                                </svg>
                                <span>Managements</span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-zinc-500 dark:text-zinc-400 transition-transform duration-200 shrink-0" :class="sections.managements ? 'rotate-90' : ''">
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </button>

                        <div x-show="sections.managements" x-cloak x-collapse>
                            <ul class="mt-1 pl-2 space-y-1">
                                <li>
                                    <a
                                        href="{{ route('admin.reports.index') }}"
                                        @click="active = 'report'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'report' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Flood Report</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">18</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'floodzone'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'floodzone' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Flood Zone</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">64</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'parkzone'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'parkzone' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Parking Zone</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">64</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'evacuation'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'evacuation' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Evacuation Route</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">64</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'assignment'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'assignment' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>SAR Assignment</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">7</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('inventories.index') }}"
                                        @click="active = 'tools'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'tools' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>SAR Tools</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">18</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Weather collapsible -->
                    <li class="pt-2 mt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <button
                            type="button"
                            class="w-full h-9 px-3 rounded-md flex items-center justify-between text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5"
                            @click="sections.weather = !sections.weather"
                            :aria-expanded="sections.weather"
                        >
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                    <rect x="9" y="3" width="6" height="4" rx="2" />
                                </svg>
                                <span>Weather</span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-zinc-500 dark:text-zinc-400 transition-transform duration-200 shrink-0" :class="sections.weather ? 'rotate-90' : ''">
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </button>

                        <div x-show="sections.weather" x-cloak x-collapse>
                            <ul class="mt-1 pl-2 space-y-1">
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'weatherstat'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'weatherstat' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Statistics</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">18</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="#_"
                                        @click="active = 'broadcast'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'broadcast' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Broadcast</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">64</span>
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="{{ route('map') }}"
                                        @click="active = 'map'"
                                        class="flex items-center justify-between h-9 px-3 rounded-md text-sm transition-colors hover:bg-zinc-100 dark:hover:bg-white/5"
                                        :class="active === 'map' ? 'bg-zinc-100 text-zinc-900 font-medium dark:bg-white/10 dark:text-white' : 'text-zinc-700 dark:text-zinc-300'"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 14v.01M12 14v.01M15 14v.01" />
                                            </svg>
                                            <span>Realtime Map</span>
                                        </span>
                                        <span class="text-[0.70rem] px-1.5 py-0.5 rounded bg-white text-zinc-900 border border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10">64</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Sticky footer -->
            <div class="mt-auto bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800">
                <div class="p-3 lg:p-4">
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-3">
                        <p class="text-xs font-medium text-zinc-700 dark:text-zinc-200">System Status</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            All services operational. Last updated 5 min ago.
                        </p>
                        <div class="mt-2.5 flex items-center justify-between">
                            <button
                                type="button"
                                class="h-8 px-3 rounded-md bg-white text-zinc-900 text-xs font-medium border border-zinc-200 hover:bg-zinc-100 dark:bg-white/10 dark:text-white dark:border-white/10 dark:hover:bg-white/15"
                            >
                                View dashboard
                            </button>
                            <div class="flex items-center gap-1.5">
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                <span class="text-xs text-zinc-600 dark:text-zinc-300">Online</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account dropdown -->
                <div class="px-3 pb-3 lg:px-4 lg:pb-4">
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            class="w-full flex items-center justify-between gap-3 p-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-white/5"
                            @click="open = !open"
                            :aria-expanded="open"
                        >
                            <span class="flex items-center gap-3 min-w-0">
                                <img src="https://i.pravatar.cc/40?img=12" alt="Avatar" class="size-8 rounded-full shrink-0" />
                                <span class="text-left truncate">
                                    <span class="block text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate">{{ auth()->user()->name ?? 'Alex W.' }}</span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->email ?? 'alex@orbitkit.dev' }}</span>
                                </span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 text-zinc-600 dark:text-zinc-300 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''">
                                <path d="M6 9l6 6l6 -6" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition.opacity
                            @click.outside="open = false"
                            class="absolute left-0 right-0 bottom-full mb-2 z-50 rounded-xl bg-white dark:bg-zinc-900 shadow-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden"
                        >
                            <div class="p-2 text-sm">
                                <a href="#_" class="flex items-center gap-2 h-9 px-2.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 9h.01M11 12h1v4h1" />
                                    </svg>
                                    <span>View profile</span>
                                </a>
                                <a href="#_" class="flex items-center gap-2 h-9 px-2.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                        <circle cx="12" cy="12" r="3" />
                                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                                    </svg>
                                    <span>Account</span>
                                </a>
                                <a href="#_" class="flex items-center gap-2 h-9 px-2.5 rounded-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-white/5">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4 shrink-0">
                                        <circle cx="12" cy="12" r="4" />
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M15 15l3.35 3.35M9 15l-3.35 3.35M5.65 5.65l3.35 3.35M18.35 5.65l-3.35 3.35" />
                                    </svg>
                                    <span>Docs</span>
                                </a>
                            </div>
                            <div class="border-t border-zinc-100 dark:border-zinc-800 p-2 bg-zinc-50 dark:bg-zinc-800/50">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full h-8 px-3 flex items-center justify-center text-xs font-medium rounded-md text-zinc-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                    >
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 min-w-0 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>