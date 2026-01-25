@extends($layout ?? 'layouts.user')

@section('title', 'Уведомления - Cliently')
@section('page-title', 'Уведомления')
@section('page-description', 'Все ваши уведомления')

@push('breadcrumbs')
    <x-breadcrumbs :items="[['title' => 'Уведомления', 'url' => null]]" />
@endpush

@section('content')

<!-- Flash сообщения -->
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-emerald-50 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-700/50 rounded-lg p-5 flex items-center gap-4 shadow-sm mb-6">
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
        </div>
        <button @click="show = false"
            class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="bg-rose-50 dark:bg-rose-500/20 border border-rose-200 dark:border-rose-700/50 rounded-lg p-5 flex items-center gap-4 shadow-sm mb-6">
        <div class="flex-shrink-0">
            <div class="h-10 w-10 rounded-lg bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center">
                <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ session('error') }}</p>
        </div>
        <button @click="show = false"
            class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
@endif

<div class="space-y-6">
    <!-- Заголовок с действиями -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Уведомления</h2>
            <p class="text-slate-600 dark:text-slate-400 mt-1">
                @if($unreadCount > 0)
                    У вас {{ $unreadCount }} {{ $unreadCount === 1 ? 'непрочитанное уведомление' : 'непрочитанных уведомлений' }}
                @else
                    Все уведомления прочитаны
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
        @if(($layout ?? 'layouts.user') === 'layouts.panel')
            <a href="{{ route('panel.settings.notifications.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg font-medium transition-all duration-200">
                <i class="fa-solid fa-gear text-sm"></i>
                Настройки уведомлений
            </a>
        @endif
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-check-double text-sm"></i>
                    Отметить все как прочитанные
                </button>
            </form>
        @endif
        </div>
    </div>

    <!-- Фильтры -->
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
        <div class="flex items-center gap-2">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ $filter === 'all' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Все
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ $filter === 'unread' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Непрочитанные
                @if($unreadCount > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-xs font-semibold bg-rose-500 text-white rounded-full">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read']) }}"
                class="px-4 py-2 rounded-lg font-medium transition-all duration-200 {{ $filter === 'read' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Прочитанные
            </a>
        </div>
    </div>

    <!-- Список уведомлений -->
    @if($notifications->count() > 0)
        <div class="grid gap-4">
            @foreach($notifications as $notification)
                <div class="bg-white dark:bg-slate-900 rounded-xl border {{ !$notification->is_read ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50/50 dark:bg-indigo-950/30' : 'border-slate-200 dark:border-slate-800' }} p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            <!-- Иконка -->
                            <div class="shrink-0 mt-0.5">
                                @php
                                    $iconConfig = [
                                        'ticket.created' => ['icon' => 'fa-ticket', 'color' => 'indigo'],
                                        'ticket.updated' => ['icon' => 'fa-pen', 'color' => 'blue'],
                                        'ticket.comment' => ['icon' => 'fa-comment', 'color' => 'purple'],
                                        'ticket.assigned' => ['icon' => 'fa-user-check', 'color' => 'indigo'],
                                        'appointment.created' => ['icon' => 'fa-calendar-check', 'color' => 'green'],
                                        'appointment.updated' => ['icon' => 'fa-calendar', 'color' => 'blue'],
                                        'appointment.upcoming' => ['icon' => 'fa-clock', 'color' => 'amber'],
                                        'appointment.cancelled' => ['icon' => 'fa-calendar-xmark', 'color' => 'red'],
                                        'subscription.limit' => ['icon' => 'fa-exclamation-triangle', 'color' => 'amber'],
                                        'subscription.expiring' => ['icon' => 'fa-clock-rotate-left', 'color' => 'red'],
                                        'client.new' => ['icon' => 'fa-user-plus', 'color' => 'indigo'],
                                        // Админские уведомления
                                        'admin.business.created' => ['icon' => 'fa-building', 'color' => 'green'],
                                        'admin.business.deleted' => ['icon' => 'fa-building', 'color' => 'red'],
                                        'admin.business.inactive' => ['icon' => 'fa-building', 'color' => 'amber'],
                                        'admin.ticket.created' => ['icon' => 'fa-ticket', 'color' => 'indigo'],
                                        'admin.ticket.critical' => ['icon' => 'fa-exclamation-circle', 'color' => 'red'],
                                        'admin.user.created' => ['icon' => 'fa-user-plus', 'color' => 'blue'],
                                        'admin.subscription.expiring' => ['icon' => 'fa-clock-rotate-left', 'color' => 'amber'],
                                        'admin.subscription.limit.exceeded' => ['icon' => 'fa-exclamation-triangle', 'color' => 'amber'],
                                        'admin.system.error' => ['icon' => 'fa-triangle-exclamation', 'color' => 'red'],
                                        'admin.broadcast' => ['icon' => 'fa-paper-plane', 'color' => 'indigo'],
                                    ];
                                    $config = $iconConfig[$notification->type] ?? ['icon' => 'fa-bell', 'color' => 'slate'];
                                    $colorClasses = [
                                        'indigo' => 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400',
                                        'blue' => 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400',
                                        'purple' => 'bg-purple-100 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400',
                                        'green' => 'bg-green-100 dark:bg-green-500/20 text-green-600 dark:text-green-400',
                                        'amber' => 'bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400',
                                        'red' => 'bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400',
                                        'slate' => 'bg-slate-100 dark:bg-slate-500/20 text-slate-600 dark:text-slate-400',
                                    ];
                                @endphp
                                <div class="h-10 w-10 rounded-lg flex items-center justify-center {{ $colorClasses[$config['color']] }}">
                                    <i class="fa-solid {{ $config['icon'] }} text-sm"></i>
                                </div>
                            </div>
                            
                            <!-- Контент -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-1">
                                            {{ $notification->title }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                                            {{ $notification->message }}
                                        </p>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-500">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-clock text-xs"></i>
                                                {{ $notification->created_at->format('d.m.Y H:i') }}
                                            </span>
                                            @if(!$notification->is_read)
                                                <span class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-xs font-medium">
                                                    Новое
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Действия -->
                        <div class="flex items-start gap-2 shrink-0">
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                        title="Отметить как прочитанное">
                                        <i class="fa-solid fa-check text-sm"></i>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="inline"
                                onsubmit="return confirm('Вы уверены, что хотите удалить это уведомление?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="h-9 w-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 hover:text-rose-600 dark:hover:text-rose-400 transition-colors"
                                    title="Удалить">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Ссылка на связанный объект -->
                    @if($notification->data && isset($notification->data['url']))
                        <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <a href="{{ $notification->data['url'] }}"
                                class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                                Перейти к объекту
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-bell text-3xl text-slate-400 dark:text-slate-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">
                    @if($filter === 'unread')
                        Нет непрочитанных уведомлений
                    @elseif($filter === 'read')
                        Нет прочитанных уведомлений
                    @else
                        Нет уведомлений
                    @endif
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    @if($filter === 'unread')
                        Все уведомления прочитаны. Новые уведомления появятся здесь.
                    @elseif($filter === 'read')
                        У вас пока нет прочитанных уведомлений.
                    @else
                        У вас пока нет уведомлений. Новые уведомления появятся здесь автоматически.
                    @endif
                </p>
            </div>
        </div>
    @endif
</div>

@endsection
