<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%');
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:vendors',
            'phone' => 'required|string',
            'country' => 'nullable|string',
            'password' => 'required|min:6',
            'logo' => 'nullable|image|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('vendors', 'public');
        }

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'password' => Hash::make($request->password),
            'logo' => $path,
            'role' => 'vendor'
        ]);

        return response()->json($vendor, 201);
    }

    public function show($id)
    {
        return response()->json(Vendor::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $vendor->update($request->only(['name', 'email', 'phone', 'country']));

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('vendors', 'public');
            $vendor->update(['logo' => $path]);
        }

        return response()->json($vendor);
    }

    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();
        return response()->json(['message' => 'Vendor deleted']);
    }
}