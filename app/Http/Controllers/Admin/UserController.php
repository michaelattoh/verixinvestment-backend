<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    // list (paginated + search)
    public function index(Request $request)
    {
        // ensure admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name','like','%'.$s.'%')
                  ->orWhere('email','like','%'.$s.'%')
                  ->orWhere('phone','like','%'.$s.'%');
            });
        }

        $users = $query->orderBy('created_at','desc')->paginate(10);

        return response()->json($users);
    }

    // create user
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'nullable|string|min:6',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        $path = null;
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'country' => $validated['country'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'profile_picture' => $path,
            'password' => Hash::make($validated['password'] ?? 'password'), // change default as needed
            'role' => 'investor',
        ]);

        return response()->json($user, 201);
    }

    // show user details
    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // update
    public function update(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string',
            'country' => 'nullable|string',
            'gender' => 'nullable|in:male,female,other',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            // delete old if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profiles', 'public');
        }

        $user->update($validated);

        return response()->json($user);
    }

    // delete
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message'=>'Forbidden'], 403);
        }

        $user = User::findOrFail($id);
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}