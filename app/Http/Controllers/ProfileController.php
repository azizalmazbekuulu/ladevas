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
        $request->validate([
            'name' => 'string|max:255',
            'user_name' =>  'string|min:4|max:20',
            'avatar' => 'image|dimensions:width=256,height=256',
            'email' => 'string|email|max:255|unique:users',
            'password' => [Rules\Password::defaults()],
        ]);
        $user->update([
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'user_role' => $request->user_role === User::ADMIN || $request->user_role === User::USER ? $request->user_role : User::USER,
            'registered_at' => time(),
            'password' => Hash::make($request->password),
        ]);

        if ($request->file('avatar')) {
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
            $user->save();
        }

        return json_encode(['status' => 'Successfully updated profile']);
    }
}
