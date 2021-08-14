<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function sendInvitaionEmail()
    {
        $email = $_POST['email'];
        $link = url("/api/register?email=$email");
        $message = "
            Hi!
            This is an invtation to register:
            $link
            Greetings!
            Ladevas team!
        ";
        if( mail($email, "Invitation to register on Ladevas", $message)) {
            return json_encode(["status" => "Successfully send invitaion email to $email"]);
        } else {
            return json_encode(["status" => "Error sending an invitaion email to $email"]);
        }
    }

    public function register()
    {
        return isset($_GET['email']) ?
                    json_encode(['status' => true, 'form' => "Form with hidden email and two fields."]) :
                    json_encode(['status' => false, 'form' => "Error."]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'user_name' =>  'string|min:4|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
        ]);
        $user = User::create([
            'name' => $request->user_name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'registered_at' => null,
            'user_role' => $request->user_role === User::ADMIN || $request->user_role === User::USER ? $request->user_role : User::USER,
            'password' => Hash::make($request->password),
        ]);

        $confirmation_code = mt_rand(100000,999999);
        $user->avatar = $confirmation_code;
        $user->save();

        if (mail($user->email,"Registration confirmation number", "Hello!
        This is the number to confirm your registration on Ladevas:
        ". $confirmation_code))
        {
            return json_encode(['status' => true, 'user_id' => $user->id]);
        }
        return json_encode(['status' => false]);
    }

    public function confirmRegistration(Request $request, User $user)
    {
        if ($request->code === $user->avatar)
        {
            $user->registered_at = time();
            $user->avatar = null;
            $user->save();
    
            Auth::login($user);

            return json_encode(['status' => true]);
        }
        return json_encode(['status' => false]);
    }
}
