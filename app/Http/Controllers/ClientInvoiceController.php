<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientInvoiceController extends Controller
{
    /**
     * Список своих инвойсов (подписка, пополнение баланса и т.д.)
     */
    public function index(Request $request)
    {
        $this->authorizeBusinessPermission('client.subscription.view');

        $query = Invoice::where('user_id', Auth::id())
            ->with(['plan', 'subscription']);

        $status = $request->input('status', '');
        if ($status !== '') {
            $query->where('status', $status);
        }

        $paymentType = $request->input('payment_type', '');
        if ($paymentType !== '') {
            $query->where('payment_type', $paymentType);
        }

        $query->orderBy('created_at', 'desc');
        $invoices = $query->paginate(20)->withQueryString();

        $paymentTypes = $this->getPaymentTypeLabels();

        return view('invoices.index', [
            'invoices' => $invoices,
            'status' => $status,
            'payment_type' => $paymentType,
            'paymentTypes' => $paymentTypes,
        ]);
    }

    /**
     * Просмотр одного инвойса (только своего)
     */
    public function show(Invoice $invoice)
    {
        $this->authorizeBusinessPermission('client.subscription.view');

        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещён.');
        }

        $invoice->load(['plan', 'subscription']);
        $paymentTypeLabel = $this->getPaymentTypeLabel($invoice->payment_type);
        $retryUrl = $this->getRetryUrlForInvoice($invoice);
        $canPay = $invoice->isPending() && ! $invoice->isExpired();

        return view('invoices.show', [
            'invoice' => $invoice,
            'paymentTypeLabel' => $paymentTypeLabel,
            'retryUrl' => $retryUrl,
            'canPay' => $canPay,
        ]);
    }

    /**
     * Название типа оплаты для отображения
     */
    protected function getPaymentTypeLabel(?string $type): string
    {
        if (! $type) {
            return '—';
        }

        return config("payments.types.{$type}.name", $type);
    }

    /**
     * Все типы оплаты для фильтра (ключ => название)
     */
    protected function getPaymentTypeLabels(): array
    {
        $types = config('payments.types', []);
        $result = [];
        foreach ($types as $key => $config) {
            if (! empty($config['name'])) {
                $result[$key] = $config['name'];
            }
        }

        return $result;
    }

    /**
     * URL для «Оформить заново» / «Попробовать снова» в зависимости от типа инвойса
     */
    protected function getRetryUrlForInvoice(Invoice $invoice): ?string
    {
        $type = $invoice->payment_type ?? 'subscription';
        if ($type === 'subscription') {
            return route('subscription.index');
        }
        if ($type === 'balance') {
            return route('dashboard');
        }

        return route('dashboard');
    }
}
