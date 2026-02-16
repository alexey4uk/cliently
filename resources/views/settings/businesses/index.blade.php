@extends('layouts.user')

@section('title', 'Бизнесы - Cliently')
@section('page-title', 'Бизнесы')
@section('page-description', 'Переключение между проектами, редактирование и добавление')

@push('breadcrumbs')
    <x-breadcrumbs :items="[
        ['title' => 'Настройки', 'url' => route('settings.index')],
        ['title' => 'Бизнесы', 'url' => null],
    ]" />
@endpush

@section('content')

<div class="max-w-4xl mx-auto px-3 sm:px-0" x-data>
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 sm:mb-6">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white break-words">Бизнесы</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 break-words">Переключайтесь между проектами, редактируйте данные или добавьте новый бизнес</p>
                @if(isset($businessLimit) && $businessLimit !== -1)
                    <p class="text-sm mt-1 break-words {{ !($canCreateBusiness ?? true) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-500 dark:text-slate-400' }}">
                        У вас {{ $businessUsage ?? 0 }} из {{ $businessLimit }} бизнесов
                        @if(!($canCreateBusiness ?? true))
                            — достигнут лимит по тарифу. <a href="{{ route('subscription.index') }}" class="underline hover:no-underline">Обновить тариф</a>
                        @endif
                    </p>
                @endif
            </div>
            @if(!$userBusinesses->isEmpty() && ($canCreateBusiness ?? true) && ($canCreateBusinessByPermission ?? true))
                <a href="{{ route('settings.business.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 sm:py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 border border-transparent rounded-lg transition-colors shrink-0 min-h-[44px] sm:min-h-0">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Добавить бизнес</span>
                </a>
            @endif
        </div>

        @if($userBusinesses->isEmpty())
            <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">У вас пока нет бизнесов.</p>
            @if(($canCreateBusiness ?? true) && ($canCreateBusinessByPermission ?? true))
                <a href="{{ route('settings.business.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors min-h-[48px]">
                    <i class="fa-solid fa-plus"></i>
                    <span>Создать первый бизнес</span>
                </a>
            @elseif(!($canCreateBusiness ?? true))
                <p class="text-amber-600 dark:text-amber-400 text-sm break-words">Достигнут лимит бизнесов по вашему тарифу. <a href="{{ route('subscription.index') }}" class="underline hover:no-underline">Обновите тариф</a>, чтобы создать новый бизнес.</p>
            @endif
        @else
            <ul class="space-y-3">
                @foreach($userBusinesses as $b)
                    <li class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-3 p-4 rounded-lg border border-slate-200 dark:border-slate-700 {{ $b->id === $business?->id ? 'bg-indigo-50/50 dark:bg-indigo-500/10 border-indigo-200 dark:border-indigo-500/30' : '' }}">
                        <div class="min-w-0 flex-1">
                            <span class="font-medium text-slate-900 dark:text-white break-words block">{{ $b->name }}</span>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                @if($b->type)
                                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $b->type === \App\Models\Business::TYPE_MASTER ? 'Мастер' : 'Организация' }}</span>
                                @endif
                                @if($b->id === $business?->id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300">Текущий</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row flex-wrap gap-2 w-full sm:w-auto">
                            @if($b->id !== $business?->id)
                                <form method="POST" action="{{ route('settings.business.switch') }}" class="w-full sm:w-auto min-w-0">
                                    @csrf
                                    <input type="hidden" name="business_id" value="{{ $b->id }}">
                                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 min-h-[44px] text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                                        Переключиться
                                    </button>
                                </form>
                            @else
                                @if($canUpdateBusiness ?? false)
                                <a href="{{ route('settings.business.edit') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 min-h-[44px] text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/30 transition-colors">
                                    Редактировать
                                </a>
                                @endif
                                @if($canDeleteBusiness ?? false)
                                    <button type="button" @click="$refs.destroyModal.showModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 min-h-[44px] text-sm font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/20 rounded-lg transition-colors">
                                        Удалить
                                    </button>
                                @endif
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if($business && ($canDeleteBusiness ?? false))
        <dialog x-ref="destroyModal" class="rounded-xl shadow-xl p-0 max-w-md w-full mx-auto border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 m-4 sm:m-auto" @click="if ($event.target === $el) $el.close()">
            <div class="p-4 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Удалить бизнес?</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">Вы уверены, что хотите удалить бизнес «{{ $business->name }}»? Это действие нельзя отменить.</p>
                <div class="flex flex-wrap gap-3 justify-end">
                    <button type="button" @click="$refs.destroyModal.close()" class="px-4 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors">Отмена</button>
                    <form action="{{ route('settings.business.destroy') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors">Удалить</button>
                    </form>
                </div>
            </div>
        </dialog>
        @endif
    </div>

    <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Назад к настройкам</span>
    </a>
</div>

@endsection
