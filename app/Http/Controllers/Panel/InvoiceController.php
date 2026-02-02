<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $search = request('search', '');
        $status = request('status', '');
        $sort = request('sort', 'created_at');
        $direction = request('direction', 'desc');
        $perPage = request('per_page', 20);
        $userId = request('user_id', '');

        $query = Invoice::with(['user', 'plan', 'subscription']);

        // Поиск
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('bepaid_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('plan', function ($planQuery) use ($search) {
                        $planQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Фильтр по статусу
        if ($status !== '') {
            $query->where('status', $status);
        }

        // Фильтр по пользователю
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Сортировка
        $allowedSorts = ['created_at', 'amount', 'paid_at', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $invoices = $query->paginate($perPage)->withQueryString();

        // Статистика
        $stats = [
            'total' => Invoice::count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'failed' => Invoice::where('status', 'failed')->count(),
            'total_amount' => Invoice::where('status', 'paid')->sum('amount'),
        ];

        return view('panel.invoices.index', compact(
            'invoices',
            'search',
            'status',
            'sort',
            'direction',
            'perPage',
            'userId',
            'stats'
        ));
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'plan', 'subscription']);

        return view('panel.invoices.show', compact('invoice'));
    }

    /**
     * Refund an invoice.
     */
    public function refund(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:'.$invoice->amount,
        ]);

        try {
            $bepaidService = app(BepaidService::class);
            $refundAmount = $request->input('amount');

            $result = $paymentGateway->refund($invoice, $refundAmount);

            return redirect()->route('panel.invoices.show', $invoice)
                ->with('success', 'Возврат средств успешно выполнен.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при возврате средств: '.$e->getMessage());
        }
    }
}
