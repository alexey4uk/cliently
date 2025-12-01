<div class="hidden lg:block bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Левая часть - Только заголовок страницы или хлебные крошки -->
            <div class="flex items-center">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @if(isset($title))
                        {{ $title }}
                    @else
                        Добро пожаловать, {{ auth()->user()->first_name }}!
                    @endif
                </h1>
            </div>

            <!-- Правая часть - Действия и профиль -->
            <div class="flex items-center space-x-3">
                <!-- Кнопка создания -->
                <button class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Новая запись
                </button>

                <!-- Поиск -->
                <button class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Переключатель темы -->
                <button id="theme-toggle-desktop" class="flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                    <svg id="theme-light-icon-desktop" class="w-5 h-5 hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                    <svg id="theme-dark-icon-desktop" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Уведомления -->
                <button class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium border-2 border-white dark:border-gray-900">
                        3
                    </span>
                </button>

                <!-- Профиль -->
                <div class="relative">
                    <button id="user-menu-button-desktop" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Аватар" class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800 shadow-md">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium border-2 border-white dark:border-gray-800 shadow-md">
                                <span class="text-sm font-bold">
                                    {{ Str::substr(auth()->user()->first_name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>

                    <!-- Выпадающее меню профиля -->
                    <div id="user-menu-desktop" class="hidden absolute right-0 mt-2 w-64 rounded-b-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 z-50">
                        <!-- Заголовок профиля -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-user-circle w-4 mr-3 text-gray-400"></i>
                                Мой профиль
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-cog w-4 mr-3 text-gray-400"></i>
                                Настройки
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-credit-card w-4 mr-3 text-gray-400"></i>
                                Тарифы и оплата
                            </a>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 py-2">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-question-circle w-4 mr-3 text-gray-400"></i>
                                Помощь и поддержка
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <i class="fas fa-shield-alt w-4 mr-3 text-gray-400"></i>
                                Безопасность
                            </a>
                        </div>

                        <!-- Футер меню -->
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2 bg-gray-50 dark:bg-gray-700/50">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Тариф:</span>
                                <span class="text-xs font-semibold text-gray-900 dark:text-white">Бесплатный</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 10%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">3 из 30 клиентов</p>
                        </div>

                        <!-- Выход -->
                        <div class="border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt w-4 mr-3"></i>
                                    Выйти из аккаунта
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
