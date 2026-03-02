<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\GatewayManager;
use App\Services\PaymentService;
use App\Services\PaymentSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    protected PaymentSettingsService $settingsService;

    protected GatewayManager $gatewayManager;

    public function __construct(
        PaymentService $paymentService,
        PaymentSettingsService $settingsService,
        GatewayManager $gatewayManager
    ) {
        $this->paymentService = $paymentService;
        $this->settingsService = $settingsService;
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Показать страницу оплаты
     */
    public function show(Invoice $invoice)
    {
        // Проверяем, что инвойс принадлежит текущему пользователю
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем, что инвойс ещё не оплачен
        if ($invoice->isPaid()) {
            return redirect()->route('subscription.index', ['payment' => 'already_paid']);
        }

        // Получаем доступные шлюзы для типа оплаты
        $type = $invoice->payment_type ?? 'subscription';
        $availableGateways = $this->paymentService->getAvailableGatewaysForType($type);

        // Если есть URL для оплаты — показываем страницу
        $paymentUrl = $invoice->gateway_payment_url;
        $paymentMethod = $invoice->payment_method ?? 'redirect';

        return view('payments.show', [
            'invoice' => $invoice,
            'paymentUrl' => $paymentUrl,
            'paymentMethod' => $paymentMethod,
            'availableGateways' => $availableGateways,
            'currentGateway' => $invoice->gateway,
        ]);
    }

    /**
     * Создать платёж с выбранным шлюзом
     */
    public function create(Request $request, string $type)
    {
        $request->validate([
            'gateway' => 'nullable|string',
        ]);

        $user = Auth::user();
        $gateway = $request->input('gateway');

        // Данные для создания платежа зависят от типа
        $data = $request->except(['gateway', '_token']);

        $result = $this->paymentService->createPayment($type, $user, $data, $gateway);

        if (! $result->success) {
            return back()->withErrors(['payment' => $result->errorMessage]);
        }

        // Редирект на страницу оплаты шлюза
        return redirect($result->redirectUrl);
    }

    /**
     * Callback успешной оплаты
     */
    public function success(Request $request)
    {
        $invoiceId = $request->input('invoice');
        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->user_id !== Auth::id()) {
            return redirect()->route('subscription.index');
        }

        // Получаем обработчик типа оплаты
        $type = $invoice->payment_type ?? 'subscription';
        $handler = $this->paymentService->getHandler($type);

        return redirect($handler->getSuccessRedirectUrl($invoice));
    }

    /**
     * Callback неуспешной оплаты
     */
    public function fail(Request $request)
    {
        $invoiceId = $request->input('invoice');
        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->user_id !== Auth::id()) {
            return redirect()->route('subscription.index');
        }

        $type = $invoice->payment_type ?? 'subscription';
        $handler = $this->paymentService->getHandler($type);

        return redirect($handler->getFailRedirectUrl($invoice));
    }

    /**
     * Callback отмены оплаты
     */
    public function cancel(Request $request)
    {
        $invoiceId = $request->input('invoice');
        $invoice = Invoice::find($invoiceId);

        if ($invoice && $invoice->user_id === Auth::id()) {
            $type = $invoice->payment_type ?? 'subscription';
            $handler = $this->paymentService->getHandler($type);
            $handler->onPaymentCancelled($invoice);
        }

        return redirect()->route('subscription.index', ['payment' => 'cancelled']);
    }

    /**
     * Сменить шлюз для инвойса и создать новый платёж
     */
    public function changeGateway(Request $request, Invoice $invoice)
    {
        $request->validate([
            'gateway' => 'required|string',
        ]);

        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }

        if ($invoice->isPaid()) {
            return back()->withErrors(['payment' => 'Инвойс уже оплачен']);
        }

        $gateway = $request->input('gateway');
        $type = $invoice->payment_type ?? 'subscription';

        // Проверяем, что шлюз разрешён для этого типа
        $allowedGateways = $this->settingsService->getAllowedGatewaysForType($type);
        if (! in_array($gateway, $allowedGateways)) {
            return back()->withErrors(['gateway' => 'Выбранный шлюз недоступен']);
        }

        // Получаем шлюз и создаём новый платёж
        $gatewayInstance = $this->gatewayManager->get($gateway);
        $handler = $this->paymentService->getHandler($type);

        $result = $gatewayInstance->createPayment($invoice, [
            'description' => $handler->getPaymentDescription($invoice),
        ]);

        if (! $result->success) {
            return back()->withErrors(['payment' => $result->errorMessage]);
        }

        // Обновляем инвойс
        $invoice->update([
            'gateway' => $gateway,
            'gateway_transaction_id' => $result->transactionId,
            'gateway_payment_url' => $result->redirectUrl,
            'gateway_response' => $result->rawResponse,
            'bepaid_payment_token' => $result->paymentToken,
        ]);

        return redirect($result->redirectUrl);
    }
}
