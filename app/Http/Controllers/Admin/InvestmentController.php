<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Investment::with('user');

        // filter by investment type
        if ($request->has('type')) {
        $query->where('type', $request->type);
    }
        
    // search by user name/email/transaction ID
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            })->orWhere('transaction_id', 'like', "%$search%");
        }

        // date filter
        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function show($id)
    {
        return response()->json(
            Investment::with('user')->findOrFail($id)
        );
    }
}