@extends('appointments.public.layout')

@section('title', 'Просмотр записи')

@section('content')
    <div class="max-w-xl mx-auto px-2 sm:px-4 pb-10"> {{-- pb-10 чтобы кнопки не прилипали к низу экрана --}}

        <div
            class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-2xl shadow-slate-200/50 dark:shadow-none overflow-hidden">

            <!-- Верхний блок: Время и Дата -->
            <div
                class="relative p-8 sm:p-10 text-center bg-gradient-to-b from-slate-50/50 to-transparent dark:from-slate-800/30 dark:to-transparent border-b border-slate-100 dark:border-slate-800">

                <!-- Статус в виде аккуратной точки и текста -->
                <div
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 mb-6">
                    @php
                        $colors = [
                            'confirmed' => 'bg-emerald-500',
                            'pending' => 'bg-amber-500',
                            'completed' => 'bg-indigo-500',
                            'cancelled' => 'bg-rose-500',
                        ];
                        $statusNames = [
                            'confirmed' => 'Подтверждена',
                            'pending' => 'Ожидание',
                            'completed' => 'Завершена',
                            'cancelled' => 'Отменена',
                        ];
                    @endphp
                    <span
                        class="w-2 h-2 rounded-full {{ $colors[$appointment->status] ?? 'bg-slate-400' }} animate-pulse"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-600 dark:text-slate-400">
                        {{ $statusNames[$appointment->status] ?? $appointment->status }}
                    </span>
                </div>

                <h1 class="text-6xl font-black text-slate-900 dark:text-white tracking-tighter mb-2">
                    {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                </h1>
                <p class="text-base text-slate-500 dark:text-slate-400 font-medium capitalize">
                    {{ $appointment->date->locale('ru')->isoFormat('dddd, D MMMM') }}
                </p>
            </div>

            <!-- Инфо-блоки -->
            <div class="p-6 sm:p-8 space-y-8">

                <div class="grid grid-cols-1 gap-8">
                    <!-- Услуга -->
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 shrink-0 flex items-center justify-center rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <x-icon name="sparkles" variant="solid" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Услуга</p>
                            <h4 class="text-base font-bold text-slate-900 dark:text-white leading-tight mb-1">
                                {{ $appointment->service->name }}</h4>
                            <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($appointment->final_price, 0, ',', ' ') }} Br •
                                {{ $appointment->final_duration }} мин
                            </p>
                        </div>
                    </div>

                    <!-- Мастер -->
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 shrink-0 flex items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-user-check text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Специалист</p>
                            @if ($appointment->master)
                            <h4 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $appointment->master->first_name }} {{ $appointment->master->last_name }}</h4>
                            @else
                            <h4 class="text-base font-medium text-slate-500 dark:text-slate-400">Будет назначен</h4>
                            @endif
                        </div>
                    </div>

                    <!-- Локация -->
                    @if ($appointment->location)
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 shrink-0 flex items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Место</p>
                                <h4 class="text-base font-bold text-slate-900 dark:text-white truncate">
                                    {{ $appointment->location->name }}</h4>
                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate">
                                    {{ $appointment->location->full_address }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Заметки -->
                @if ($appointment->notes)
                    <div
                        class="p-5 rounded-[1.5rem] bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Ваш комментарий</p>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed italic">
                            «{{ $appointment->notes }}»</p>
                    </div>
                @endif
            </div>

            <!-- Кнопки: Адаптированы под большой палец -->
            <div class="p-6 pt-0">
                <div class="flex flex-col gap-3">
                    @if (!in_array($appointment->status, ['completed', 'cancelled']))
                        <form method="POST"
                            action="{{ route('public.appointment.cancel', ['token' => $appointment->token]) }}"
                            class="m-0" onsubmit="return handleCancel(this);">
                            @csrf
                            <button type="submit"
                                class="group w-full h-16 flex items-center justify-center gap-3 bg-rose-500 hover:bg-rose-600 active:scale-[0.97] transition-all duration-200 rounded-[1.25rem] text-white shadow-lg shadow-rose-200 dark:shadow-none">
                                <i class="fa-solid fa-xmark transition-transform group-hover:rotate-90"></i>
                                <span class="font-black text-sm uppercase tracking-widest">Отменить визит</span>
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('public.appointments.show', $business->slug) }}"
                        class="w-full h-16 flex items-center justify-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 active:scale-[0.97] transition-all duration-200 rounded-[1.25rem] text-slate-900 dark:text-white">
                        <i class="fa-solid fa-plus text-indigo-500"></i>
                        <span class="font-black text-sm uppercase tracking-widest">Новая запись</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleCancel(form) {
            if (confirm('Отменить эту запись?')) {
                const btn = form.querySelector('button');
                btn.innerHTML = '<i class="fa-solid fa-circle-notch animate-spin"></i>';
                btn.classList.add('opacity-80', 'pointer-events-none');
                return true;
            }
            return false;
        }
    </script>

    <style>
        /* Убираем синий блик при нажатии на мобильных устройствах */
        * {
            -webkit-tap-highlight-color: transparent;
        }

        /* Плавная анимация появления контента */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .max-w-xl {
            animation: slideIn 0.4s ease-out;
        }
    </style>
@endsection
