<div class="hidden lg:flex lg:flex-shrink-0">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 pt-5 pb-4 overflow-y-auto border-r border-gray-200 dark:border-gray-700">
            <!-- Логотип -->
            <div class="flex items-center flex-shrink-0 px-4">
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ config('app.name', 'Мастерская CRM') }}</span>
            </div>

            <!-- Навигация -->
            <div class="mt-8 flex-grow flex flex-col">
                <nav class="flex-1 px-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="{{ Request::routeIs('dashboard') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('dashboard') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Панель управления
                    </a>
                    <a href="" class="{{ Request::routeIs('clients.*') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('clients.*') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Клиенты
                    </a>
                    <a href="" class="{{ Request::routeIs('appointments.*') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('appointments.*') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Записи
                    </a>
                    <a href="" class="{{ Request::routeIs('finance.*') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('finance.*') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Финансы
                    </a>
                    <a href="" class="{{ Request::routeIs('reports.*') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('reports.*') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Отчеты
                    </a>
                    <a href="" class="{{ Request::routeIs('settings.*') ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="{{ Request::routeIs('settings.*') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300' }} mr-3 flex-shrink-0 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Настройки
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>
