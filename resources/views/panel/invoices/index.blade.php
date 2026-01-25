@extends('layouts.panel')

@section('title', 'Платежи')

@section('content')
    <!-- Flash сообщения -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
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

    <div class="space-y-6">
        <!-- Заголовок со статистикой -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-credit-card text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Платежи</h1>
                        <p class="text-slate-600 dark:text-slate-400 mt-1">Управление платежами и инвойсами</p>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Всего</p>
                </div>
                <div class="bg-amber-50 dark:bg-amber-500/20 rounded-lg p-4 border border-amber-200 dark:border-amber-700">
                    <p class="text-2xl font-bold text-amber-700 dark:text-amber-400">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Ожидают</p>
                </div>
                <div class="bg-emerald-50 dark:bg-emerald-500/20 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
                    <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-400">{{ $stats['paid'] }}</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">Оплачено</p>
                </div>
                <div class="bg-rose-50 dark:bg-rose-500/20 rounded-lg p-4 border border-rose-200 dark:border-rose-700">
                    <p class="text-2xl font-bold text-rose-700 dark:text-rose-400">{{ $stats['failed'] }}</p>
                    <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">Ошибки</p>
                </div>
                <div class="bg-indigo-50 dark:bg-indigo-500/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700">
                    <p class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">{{ number_format($stats['total_amount'], 2) }}</p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">Сумма (BYN)</p>
                </div>
            </div>
        </div>

        <!-- Поиск и фильтры -->
        <form method="GET" action="{{ route('panel.invoices') }}" class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Поиск -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Поиск</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="ID, транзакция, пользователь, тариф..."
                            class="pl-11 pr-4 py-3 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <!-- Статус -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Статус</label>
                    <select name="status" class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">
                        <option value="">Все</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Ожидают</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Оплачено</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>Ошибки</option>
                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Отменено</option>
                        <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Возврат</option>
                    </select>
                </div>

                <!-- Кнопки -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fa-solid fa-filter mr-2"></i>Применить
                    </button>
                    <a href="{{ route('panel.invoices') }}" class="px-4 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>

        <!-- Таблица инвойсов -->
        @if ($invoices->count() > 0)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Пользователь</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Тариф</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Сумма</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Статус</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Дата</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach ($invoices as $invoice)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">#{{ $invoice->id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $invoice->user->name }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $invoice->user->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $invoice->plan->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                                'paid' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400',
                                                'failed' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400',
                                                'cancelled' => 'bg-slate-100 text-slate-700 dark:bg-slate-500/20 dark:text-slate-400',
                                                'refunded' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Ожидает',
                                                'paid' => 'Оплачено',
                                                'failed' => 'Ошибка',
                                                'cancelled' => 'Отменено',
                                                'refunded' => 'Возврат',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$invoice->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-600 dark:text-slate-400">
                                            <p>{{ $invoice->created_at->format('d.m.Y H:i') }}</p>
                                            @if($invoice->paid_at)
                                                <p class="text-xs text-emerald-600 dark:text-emerald-400">Оплачен: {{ $invoice->paid_at->format('d.m.Y H:i') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('panel.invoices.show', $invoice) }}"
                                               class="inline-flex items-center justify-center p-1.5 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-md transition-colors"
                                               title="Просмотр">
                                                <i class="fa-solid fa-eye text-sm"></i>
                                            </a>
                                            @can('panel.payments.manage')
                                                @if($invoice->isPaid() && !$invoice->isRefunded())
                                                    <button type="button"
                                                            onclick="showRefundModal({{ $invoice->id }}, {{ $invoice->amount }})"
                                                            class="inline-flex items-center justify-center p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                                                            title="Возврат">
                                                        <i class="fa-solid fa-undo text-sm"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                    {{ $invoices->links() }}
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-12 text-center">
                <div class="h-16 w-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-credit-card text-slate-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-2">Платежи не найдены</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">Попробуйте изменить фильтры поиска</p>
            </div>
        @endif
    </div>

    <!-- Модальное окно возврата -->
    <div id="refundModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Возврат средств</h3>
            <form id="refundForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Сумма возврата</label>
                    <input type="number" step="0.01" id="refundAmount" name="amount" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Оставьте пустым для полного возврата</p>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRefundModal()"
                        class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Отмена
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                        Выполнить возврат
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRefundModal(invoiceId, maxAmount) {
            document.getElementById('refundForm').action = `/panel/invoices/${invoiceId}/refund`;
            document.getElementById('refundAmount').max = maxAmount;
            document.getElementById('refundAmount').value = '';
            document.getElementById('refundModal').classList.remove('hidden');
        }

        function closeRefundModal() {
            document.getElementById('refundModal').classList.add('hidden');
        }
    </script>
@endsection
