@extends('layouts.user')

@section('title', 'Клиенты - Cliently')
@section('page-title', 'Клиенты')
@section('page-description', 'Ваша клиентская база')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Клиенты', 'url' => null]
    ]" />
@endpush

@section('content')

<div x-data="{ 
    showPhoneModal: false, 
    phone: '', 
    phoneDisplay: '', 
    client: '',
    showDeleteModal: false,
    clientToDelete: null,
    clientName: '',
    openPhoneModal(phone, phoneDisplay, client) {
        this.phone = phone;
        this.phoneDisplay = phoneDisplay;
        this.client = client;
        this.showPhoneModal = true;
    },
    closePhoneModal() {
        this.showPhoneModal = false;
    },
    openDeleteModal(clientId, clientName) {
        this.clientToDelete = clientId;
        this.clientName = clientName;
        this.showDeleteModal = true;
    },
    closeDeleteModal() {
        this.showDeleteModal = false;
        this.clientToDelete = null;
        this.clientName = '';
    },
    confirmDelete() {
        if (this.clientToDelete) {
            const form = document.getElementById('delete-form-' + this.clientToDelete);
            if (form) {
                form.submit();
            }
        }
    }
}" class="space-y-4 md:space-y-6">
    
    <!-- Заголовок с кнопкой -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white mb-1">
                Клиенты
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Управление клиентской базой вашего бизнеса
            </p>
        </div>
        <a href="{{ route('clients.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Добавить клиента</span>
        </a>
    </div>

    <!-- Поиск и сортировка -->
    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-col sm:flex-row items-end gap-3">
        <!-- Поиск -->
        <div class="flex-1 w-full">
            <label for="client-search" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                Поиск
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-search text-slate-400 text-xs sm:text-sm"></i>
                </div>
                <input
                    id="client-search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Поиск по имени, телефону или email..."
                    class="w-full pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500"
                >
            </div>
        </div>

        <!-- Сортировка -->
        <div class="w-full sm:w-56">
            <label for="client-sort" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                Сортировка
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-sort text-slate-400 text-xs sm:text-sm"></i>
                </div>
                <select id="client-sort" name="sort" onchange="updateSortDirection(this); this.form.submit()"
                    class="w-full pl-9 sm:pl-10 pr-10 py-2 sm:py-2.5 text-sm bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:border-indigo-600 transition-all duration-150 text-slate-900 dark:text-white appearance-none cursor-pointer">
                    <option value="name" data-direction="asc" {{ $sort === 'name' && $direction === 'asc' ? 'selected' : '' }}>По имени (А-Я)</option>
                    <option value="name" data-direction="desc" {{ $sort === 'name' && $direction === 'desc' ? 'selected' : '' }}>По имени (Я-А)</option>
                    <option value="created_at" data-direction="desc" {{ $sort === 'created_at' && $direction === 'desc' ? 'selected' : '' }}>По дате добавления</option>
                </select>
                <input type="hidden" name="direction" value="{{ $direction }}" id="sort-direction">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs"></i>
                </div>
            </div>
        </div>
    </form>

    <!-- Список клиентов -->
    @if($clients->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach($clients as $client)
                <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
                    <!-- Заголовок карточки -->
                    <div class="px-4 md:px-5 py-3 md:py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('clients.show', $client) }}" class="flex items-center gap-3 min-w-0 flex-1 group">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-sm shadow-sm">
                                    {{ $client->initials }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white truncate mb-0.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $client->full_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        Клиент с {{ $client->created_at->format('d.m.Y') }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Содержимое карточки -->
                    <div class="p-4 md:p-5 space-y-3">
                        <!-- Телефон -->
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-slate-400 text-xs flex-shrink-0"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Телефон</p>
                                <button @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                        class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium break-all text-left transition-colors">
                                    {{ $client->phone }}
                                </button>
                            </div>
                        </div>

                        <!-- Email -->
                        @if($client->email)
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-envelope text-slate-400 text-xs flex-shrink-0"></i>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0.5">Email</p>
                                    <p class="text-sm text-slate-700 dark:text-slate-300 break-all truncate">
                                        {{ $client->email }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Действия -->
                    <div class="px-4 md:px-5 py-3 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/30 flex-shrink-0">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="openPhoneModal('{{ $client->phone }}', '{{ $client->phone }}', '{{ addslashes($client->full_name) }}')"
                                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/20 border border-indigo-200 dark:border-indigo-700/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors">
                                <i class="fa-solid fa-phone text-xs"></i>
                                <span>Контакт</span>
                            </button>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                </button>
                                <div x-show="open" 
                                    @click.away="open = false" 
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-48 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl z-50"
                                    style="display: none;">
                                    <a href="{{ route('clients.show', $client) }}" 
                                       @click="open = false"
                                       class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-t-lg transition-colors">
                                        <i class="fa-regular fa-eye w-4 inline-block"></i> Просмотр
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" 
                                       @click="open = false"
                                       class="block px-4 py-2.5 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                        <i class="fa-solid fa-pencil w-4 inline-block"></i> Редактировать
                                    </a>
                                    <div class="border-t border-slate-200 dark:border-slate-800 my-1"></div>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" 
                                          id="delete-form-{{ $client->id }}"
                                          class="w-full">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button @click="open = false; openDeleteModal({{ $client->id }}, '{{ addslashes($client->full_name) }}')" 
                                            class="w-full text-left px-4 py-2.5 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 rounded-b-lg transition-colors">
                                        <i class="fa-solid fa-trash w-4 inline-block"></i> Удалить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if($clients->hasPages())
            <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm px-4 py-3">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        Показано <span class="font-medium text-slate-900 dark:text-white">{{ $clients->firstItem() }}</span> - 
                        <span class="font-medium text-slate-900 dark:text-white">{{ $clients->lastItem() }}</span> из 
                        <span class="font-medium text-slate-900 dark:text-white">{{ $clients->total() }}</span> клиентов
                    </div>

                    <div class="flex items-center space-x-1">
                        @if($clients->onFirstPage())
                            <button disabled
                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </button>
                        @else
                            <a href="{{ $clients->previousPageUrl() }}"
                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        @foreach($clients->getUrlRange(1, min(5, $clients->lastPage())) as $page => $url)
                            @if($page == $clients->currentPage())
                                <button disabled
                                    class="w-8 h-8 flex items-center justify-center bg-indigo-600 text-white rounded-lg font-medium cursor-default text-xs sm:text-sm">
                                    {{ $page }}
                                </button>
                            @else
                                <a href="{{ $url }}"
                                    class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300 text-xs sm:text-sm">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($clients->hasMorePages())
                            <a href="{{ $clients->nextPageUrl() }}"
                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-700 dark:text-slate-300">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <button disabled
                                class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg opacity-50 cursor-not-allowed text-slate-400">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Пустое состояние -->
        <div class="bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm p-8 md:p-12 text-center">
            <div class="max-w-sm mx-auto">
                <div class="h-16 w-16 rounded-xl bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    @if($search)
                        Клиенты не найдены
                    @else
                        База клиентов пуста
                    @endif
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                    @if($search)
                        Попробуйте изменить параметры поиска или очистить фильтры для получения других результатов
                    @else
                        Начните работу с системой, добавив первого клиента в вашу базу данных
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if($search)
                        <a href="{{ route('clients.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                            <span>Очистить поиск</span>
                        </a>
                    @endif
                    <a href="{{ route('clients.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>Добавить клиента</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Модальное окно для номера телефона -->
    <div x-show="showPhoneModal" 
         @click.away="closePhoneModal()"
         @keydown.escape.window="closePhoneModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Контакт</h3>
                <button @click="closePhoneModal()" 
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <div class="mb-4">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Клиент</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-indigo-600 dark:text-indigo-300"></i>
                        </div>
                        <p class="text-base font-semibold text-slate-900 dark:text-white" x-text="client"></p>
                    </div>
                </div>
                <div class="mb-6">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-2 uppercase tracking-wide">Телефон</p>
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-phone text-emerald-600 dark:text-emerald-300"></i>
                        </div>
                        <p class="text-xl font-bold text-slate-900 dark:text-white" x-text="phoneDisplay"></p>
                    </div>
                </div>
                <div class="space-y-2">
                    <a :href="`tel:${phone}`"
                        class="md:hidden w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-medium text-white hover:bg-indigo-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-solid fa-phone text-sm"></i>
                        <span>Позвонить</span>
                    </a>
                    <button @click="navigator.clipboard.writeText(phone); closePhoneModal();"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 md:bg-slate-100 md:dark:bg-slate-800 px-4 py-3 text-sm font-medium text-white md:text-slate-700 md:dark:text-slate-300 hover:bg-indigo-700 md:hover:bg-slate-200 md:dark:hover:bg-slate-700 active:bg-indigo-800 transition-colors">
                        <i class="fa-regular fa-copy text-sm"></i>
                        <span>Копировать номер</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" 
         @click.away="closeDeleteModal()"
         @keydown.escape.window="closeDeleteModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         style="display: none;">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="bg-white dark:bg-slate-900 rounded-lg shadow-xl border border-slate-200 dark:border-slate-800 max-w-sm w-full">
            <div class="flex items-center justify-between px-4 md:px-6 pt-4 md:pt-5 pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-base md:text-lg font-semibold text-slate-900 dark:text-white">Подтверждение удаления</h3>
                <button @click="closeDeleteModal()" 
                        class="h-8 w-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            <div class="px-4 md:px-6 py-4 md:py-5">
                <p class="text-sm md:text-base text-slate-700 dark:text-slate-300 mb-6">
                    Вы уверены, что хотите удалить клиента <span class="font-semibold" x-text="clientName"></span>? Это действие нельзя отменить.
                </p>
                <div class="flex gap-3">
                    <button @click="closeDeleteModal()" 
                            class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </button>
                    <button @click="confirmDelete()" 
                            class="flex-1 px-4 py-2.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-medium transition-colors">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateSortDirection(select) {
    const selectedOption = select.options[select.selectedIndex];
    const direction = selectedOption.getAttribute('data-direction');
    document.getElementById('sort-direction').value = direction;
}
</script>

@endsection
