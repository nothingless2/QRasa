<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * Store a new expense for the active shift.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1|max:10000000',
            'description' => 'required|string|max:255',
        ]);

        // Find the active shift for this cashier
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif. Buka shift terlebih dahulu.',
            ], 400);
        }

        // Rate limiting: max 20 expenses per shift
        $expenseCount = $shift->expenses()->count();
        if ($expenseCount >= 20) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 20 catatan pengeluaran per shift.',
            ], 429);
        }

        $expense = Expense::create([
            'shift_id'    => $shift->id,
            'user_id'     => Auth::id(),
            'amount'      => $request->amount,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dicatat.',
            'expense' => $expense,
            'total_expenses' => $shift->expenses()->sum('amount'),
        ]);
    }

    /**
     * Get all expenses for the current active shift.
     */
    public function getShiftExpenses()
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json(['expenses' => [], 'total' => 0]);
        }

        $expenses = $shift->expenses()->latest()->get();
        $total = $shift->expenses()->sum('amount');

        return response()->json([
            'expenses' => $expenses,
            'total'    => $total,
        ]);
    }
}
