<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mediflow - Gestión Médica Inteligente</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .hero-pattern {
            background-color: #f0f9ff;
            background-image: radial-gradient(#bae6fd 0.5px, transparent 0.5px), radial-gradient(#bae6fd 0.5px, #f0f9ff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.5;
        }
    </style>
</head>

<body class="antialiased text-slate-800 bg-white selection:bg-cyan-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass-nav border-b border-white/20 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <!-- Logo placeholder or icon -->
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                        M</div>
                    <span class="font-bold text-2xl tracking-tight text-slate-900">Mediflow</span>
                </div>

                <div class="hidden md:block">
                    <livewire:welcome.navLink />
                </div>

                <div class="hidden md:flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="font-semibold text-slate-600 hover:text-cyan-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="font-medium text-slate-600 hover:text-cyan-600 transition">Iniciar Sesión</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-6 py-2.5 rounded-full bg-slate-900 text-white font-semibold hover:bg-slate-800 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    Comenzar Gratis
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="text-slate-600 hover:text-slate-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 z-0 hero-pattern"></div>
        <div
            class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-cyan-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob">
        </div>
        <div
            class="absolute top-0 left-0 -mt-20 -ml-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="max-w-2xl text-center lg:text-left">
                    <div
                        class="inline-flex items-center px-4 py-2 rounded-full border border-cyan-100 bg-cyan-50 text-cyan-700 text-sm font-medium mb-6">
                        <span class="flex h-2 w-2 rounded-full bg-cyan-500 mr-2"></span>
                        La plataforma #1 para médicos modernos
                    </div>
                    <h1
                        class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 mb-8 leading-tight">
                        Transforma tu <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-500 to-blue-600">Consultorio
                            Médico</span>
                    </h1>
                    <p class="text-xl text-slate-600 mb-10 leading-relaxed">
                        La plataforma integral para gestionar pacientes, citas, historias clínicas y pagos en un solo
                        lugar. Dedícate a tus pacientes, nosotros nos encargamos del resto.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="#"
                            class="px-8 py-4 rounded-2xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold text-lg hover:shadow-lg hover:shadow-cyan-500/30 transition transform hover:-translate-y-1">
                            Agenda una Demo
                        </a>
                        <a href="#"
                            class="px-8 py-4 rounded-2xl bg-white text-slate-700 border border-slate-200 font-bold text-lg hover:bg-slate-50 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Ver Video
                        </a>
                    </div>

                    <div class="mt-10 flex items-center justify-center lg:justify-start gap-4 text-sm text-slate-500">
                        <div class="flex -space-x-2">
                            <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white"></div>
                            <div class="w-8 h-8 rounded-full bg-slate-300 border-2 border-white"></div>
                            <div class="w-8 h-8 rounded-full bg-slate-400 border-2 border-white"></div>
                        </div>
                        <p>Confían en nosotros +500 doctores</p>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative lg:h-[600px] flex items-center justify-center">
                    <div
                        class="absolute inset-0 bg-gradient-to-tr from-cyan-200 to-blue-200 rounded-full blur-[100px] opacity-40">
                    </div>
                    <img src="{{ asset('images/hero.png') }}" alt="Mediflow Dashboard"
                        class="relative w-full max-w-lg lg:max-w-xl mx-auto drop-shadow-2xl rounded-3xl transform hover:scale-105 transition duration-700 ease-out">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-cyan-600 font-bold tracking-wide uppercase text-sm mb-3">Características Principales
                </h2>
                <h3 class="text-4xl font-bold text-slate-900 mb-6">Todo lo que necesitas para tu práctica</h3>
                <p class="text-lg text-slate-600">Herramientas potentes diseñadas para simplificar tu flujo de trabajo
                    diario y mejorar la experiencia de tus pacientes.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-cyan-100/50 transition duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-cyan-100 flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Historial Clínico Electrónico</h4>
                    <p class="text-slate-600 leading-relaxed">Accede a antecedentes, diagnósticos y tratamientos de
                        forma centralizada y segura. Todo el historial en un solo lugar.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-blue-100/50 transition duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Agenda Inteligente</h4>
                    <p class="text-slate-600 leading-relaxed">Optimiza tu tiempo. Vista clara de citas, agendamiento
                        rápido y reducción de ausentismo.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-purple-100/50 transition duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Recetas Digitales</h4>
                    <p class="text-slate-600 leading-relaxed">Genera recetas profesionales PDF listas para imprimir o
                        enviar. Plantillas personalizables.</p>
                </div>

                <!-- Feature 4 -->
                <div
                    class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:shadow-xl hover:shadow-emerald-100/50 transition duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Facturación y Pagos</h4>
                    <p class="text-slate-600 leading-relaxed">Control total de ingresos. Generación de recibos
                        instantáneos y reportes financieros.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Section -->
    <section id="solutions" class="py-24 bg-slate-900 text-white relative overflow-hidden">
        <!-- Decorative bg elements -->
        <div
            class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-cyan-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20">
        </div>
        <div
            class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-600 rounded-full mix-blend-overlay filter blur-3xl opacity-20">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-cyan-400 font-bold tracking-wide uppercase text-sm mb-3">Soluciones a medida</h2>
                    <h3 class="text-4xl font-bold mb-6">Adaptable a tu crecimiento</h3>
                    <p class="text-slate-300 text-lg mb-8 leading-relaxed">
                        Tanto si eres un médico independiente como si gestionas una clínica con múltiples especialistas,
                        Mediflow escala contigo.
                    </p>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2">Para Médicos Independientes</h4>
                                <p class="text-slate-400">Olvídate del papeleo. Lleva tu consultorio en el bolsillo y
                                    accede desde cualquier lugar.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-cyan-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2">Para Clínicas</h4>
                                <p class="text-slate-400">Gestión multi-usuario, roles, permisos y reportes unificados
                                    de productividad.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-cyan-500 to-blue-600 transform rotate-3 rounded-3xl opacity-20">
                    </div>
                    <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700 relative shadow-2xl">
                        <!-- Simplified UI Mockup -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-700 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-600"></div>
                                    <div class="h-2 w-24 bg-slate-600 rounded"></div>
                                </div>
                                <div class="h-8 w-20 bg-cyan-600 rounded-lg"></div>
                            </div>
                            <div class="h-32 bg-slate-700/50 rounded-xl w-full"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="h-24 bg-slate-700/50 rounded-xl"></div>
                                <div class="h-24 bg-slate-700/50 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-cyan-600 font-bold tracking-wide uppercase text-sm mb-3">Planes y Precios</h2>
                <h3 class="text-4xl font-bold text-slate-900 mb-6">Inversión simple y transparente</h3>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto items-center">
                <!-- Basic Plan -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Básico</h4>
                    <p class="text-slate-500 mb-6">Para empezar tu práctica</p>
                    <div class="text-4xl font-bold text-slate-900 mb-6">$0</div>
                    <ul class="space-y-4 mb-8 text-slate-600 text-sm">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> 1 Doctor</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Hasta 50 pacientes</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Agenda Básica</li>
                    </ul>
                    <button
                        class="w-full py-3 rounded-xl border border-slate-200 font-bold text-slate-700 hover:bg-slate-50 transition">Comenzar</button>
                </div>

                <!-- Pro Plan -->
                <div
                    class="bg-white p-8 rounded-3xl shadow-xl border-2 border-cyan-500 relative transform scale-105 z-10">
                    <div
                        class="absolute top-0 right-0 bg-cyan-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-lg">
                        Recomendado</div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Profesional</h4>
                    <p class="text-slate-500 mb-6">Para especialistas activos</p>
                    <div class="text-4xl font-bold text-slate-900 mb-6">$29<span
                            class="text-lg text-slate-400 font-normal">/mes</span></div>
                    <ul class="space-y-4 mb-8 text-slate-600 text-sm">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> 1 Doctor</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Pacientes Ilimitados</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Recetas PDF</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Facturación</li>
                    </ul>
                    <button
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-bold hover:shadow-lg transition">Elegir
                        Plan</button>
                </div>

                <!-- Clinic Plan -->
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200">
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Clínica</h4>
                    <p class="text-slate-500 mb-6">Para centros médicos</p>
                    <div class="text-4xl font-bold text-slate-900 mb-6">$99<span
                            class="text-lg text-slate-400 font-normal">/mes</span></div>
                    <ul class="space-y-4 mb-8 text-slate-600 text-sm">
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> +5 Doctores</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Roles y Permisos</li>
                        <li class="flex items-center gap-2"><svg class="w-5 h-5 text-green-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg> Reportes Avanzados</li>
                    </ul>
                    <button
                        class="w-full py-3 rounded-xl border border-slate-200 font-bold text-slate-700 hover:bg-slate-50 transition">Contactar
                        Ventas</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 border-b border-slate-800 pb-12 mb-12">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
                            M</div>
                        <span class="font-bold text-xl text-white">Mediflow</span>
                    </div>
                    <p class="text-slate-400 max-w-sm">Democratizando la tecnología en el sector salud. Herramientas
                        intuitivas para profesionales que cuidan vidas.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Producto</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="hover:text-cyan-400 transition">Características</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Precios</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Seguridad</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-6">Compañía</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="hover:text-cyan-400 transition">Nosotros</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Contacto</a></li>
                        <li><a href="#" class="hover:text-cyan-400 transition">Blog</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} Mediflow. Todos los derechos reservados.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition">Privacidad</a>
                    <a href="#" class="hover:text-white transition">Términos</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
