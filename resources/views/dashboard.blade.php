@extends('layouts.user')
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Заголовок и дата -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@yield('page-title', 'Панель управления')</h1>
            <p class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::now()->translatedFormat('l, j F Y') }}</p>
        </div>

        @yield('content')

        <!-- Статистика -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Клиенты -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-indigo-100 dark:bg-indigo-900 p-3 mr-4">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Всего клиентов</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_clients'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Записи на сегодня -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mr-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Записи сегодня</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['today_appointments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Доход за месяц -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Доход за месяц</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['monthly_income'] ?? 0, 0, ',', ' ') }} ₽</p>
                    </div>
                </div>
            </div>

            <!-- Отзывы -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900 p-3 mr-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Новых отзывов</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['new_reviews'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Быстрые действия -->
        <div class="mb-8">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Быстрые действия</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <a href="" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="rounded-full bg-indigo-100 dark:bg-indigo-900 p-3 mb-2">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Новый клиент</span>
                </a>

                <a href="" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="rounded-full bg-green-100 dark:bg-green-900 p-3 mb-2">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Новая запись</span>
                </a>

                <a href="" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="rounded-full bg-yellow-100 dark:bg-yellow-900 p-3 mb-2">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Принять оплату</span>
                </a>

                <a href="" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex flex-col items-center justify-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900 p-3 mb-2">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Отчеты</span>
                </a>
            </div>
        </div>

        <!-- Ближайшие записи -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ближайшие записи</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($upcomingAppointments as $appointment)
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                                <span class="text-indigo-800 dark:text-indigo-200 font-medium">
                                                    {{ strtoupper(substr($appointment->client->name, 0, 1)) }}{{ strtoupper(substr($appointment->client->surname, 0, 1)) }}
                                                </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->client->full_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $appointment->service->name }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appointment->start_time->format('H:i') }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($appointment->start_time->isToday())
                                        Сегодня
                                    @elseif($appointment->start_time->isTomorrow())
                                        Завтра
                                    @else
                                        {{ $appointment->start_time->translatedFormat('j M') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-4 sm:px-6 text-center text-gray-500 dark:text-gray-400">
                        На сегодня записей нет
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
