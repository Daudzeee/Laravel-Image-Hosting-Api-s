<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterUserController extends Controller
{
	//Store User Information for SignUp
	public function register(UserRequest $request)
	{
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'age' => $request->age,
			'profile_image' => $request->profile_image
		]);
//		//Image_Upload
		if ($request->hasFile('profile_image') ) {
			$imageName = time() . '.' . $request->profile_image->extension();
			$request->profile_image->move(public_path('images'), $imageName);
		}
		//Create Token
		$token = Str::random(64);
		$user->user_token()->create(['token' => $token]);

		//Send Email Verification Token
		Mail::send('emailVerificationEmail', ['token' => $token],
			function ($message) use ($request) {
				$message->to($request->email);
				$message->subject('Email Verification Mail');
			});

		return response()->json(['message' => 'Registered Successfully', 'data' => $user, 'token' => $user->user_token->token]);
	}

	//Confirm Verification of User
	public function verification(Request $request)
	{
		$token = $request->header('Authorization');
		$verifyUser = UserVerification::where('token', $token)->first();
		$message = 'Sorry your email cannot be identified.';

		if (!is_null($verifyUser)) {
			$user = $verifyUser->user;

			if (!$user->is_email_verified) {
				$verifyUser->user->is_email_verified = 1;
				$verifyUser->user->email_verified_at = now();
				$verifyUser->user->save();
				$message = "Your e-mail is verified. You can now login.";
			} else {
				$message = "Your e-mail is already verified. You can now login.";
			}
		}
	}

	//Match User's record & LoggedIn
	public function Login(Request $request)
	{
		$user = User::where('email', $request->email)->first();
		if ($user) {
			if (password_verify($request->password, $user->password)) {
				return response()->json([
					'data' => $user,
					'access_token' => $user->user_token->token ?? $user->user_token()->create(['token' => Str::random(64)])->token,
				]);

			} else {
				return response()->json(['message' => "Invalid Password"]);
			}
		}
		else {
			return response()->json(['message' => "Invalid Password"]);
		}
	}

	//LogOut User
	public function logout(Request $request)
	{
		$token = $request->header('Authorization');
		$user = UserVerification::where('token', $token)->first();
		if ($user) {
			$user->delete();
			return response()->json(['message' => 'User Logged Out',]);

		} else {
			return response()->json(['message' => 'User not found',]);
		}
	}

	//LoggedIn User's View Profile
	public function profile($request)
	{
		$token = $request->header('Authorization');
		$user = UserVerification::where('token', $token)->first();
		$user = $user->user;
		if($user)
		{
			return response()->json(['message' =>"My Profile", 'data' => $user]);
		}else{
			return response()->json(['message' =>"User Not Found"]);
		}
	}

	public function update(UpdateRequest $request)
	{
		if($request->email)
		{
			return response()->json(['message' => " Cannot update the email"]);
		}
		$token = $request->header('Authorization');
		$user = UserVerification::where('token', $token)->first()->user;

		if ($request->profile_image)
		{
			$image= $user->profile_image;

				if(Storage::exists($image))
				{
					Storage::delete($image);
					$path = $request->File("profile_image")->store('Images');
				}
		}
		else
		{
		     $user->name = $request->name;
			$user->password = Hash::make($request->password);
			$user->age = $request->age;
			$path = $request->File("profile_image")->store('Images');
			$user->profile_image = $path;
			$user->save();
		}
			return response()->json(['message'=>"Updated Profile",'data' => $user]);
		}

}