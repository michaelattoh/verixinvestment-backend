<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        return response()->json(TransactionCategory::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|unique:transaction_categories,name',
            'status' => 'required|in:active,inactive',
        ]);

        $category = TransactionCategory::create($request->all());

        return response()->json($category, 201);
    }

    public function show($id)
    {
        return response()->json(TransactionCategory::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $category = TransactionCategory::findOrFail($id);

        $request->validate([
            'name'   => 'sometimes|string|unique:transaction_categories,name,' . $category->id,
            'status' => 'sometimes|in:active,inactive',
        ]);

        $category->update($request->all());

        return response()->json($category);
    }
}
