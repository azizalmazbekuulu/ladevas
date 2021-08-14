<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function update(Request $request, User $user)
    {
        if ($user != auth()->user()) {
            return json_encode(['status' => 'Invalid user profile']);
        }
        $request->validate([
            'name' => 'string|max:255',
            'user_name' =>  'string|min:4|max:20',
            'avatar' => 'image|dimensions:width=256,height=256',
            'email' => 'string|email|max:255|unique:users',
            'password' => [Rules\Password::defaults()],
        ]);
        $user->update([
            'name' => $request->name,// != null ? $request->name : $user->name,
            'user_name' => $request->user_name != null ? $request->name : $user->name,
            'email' => $request->email ? $request->name : $user->name,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        if ($request->file('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            $user->save();
        }

        return json_encode(['status' => 'Successfully updated profile']);
    }
}
