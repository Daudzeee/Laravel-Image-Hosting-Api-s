<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{

	public function forgotPassword(Request $request)
	{
		$request->validate([
			'email' => 'required|email|exists:users',
		]);

		$token = Str::random(64);

		DB::table('password_resets')->insert([
			'email' => $request->email,
			'token' => $token,
			'created_at' => Carbon::now()
		]);

		Mail::send('email.forgetPassword', ['token' => $token], function($message) use($request){
			$message->to($request->email);
			$message->subject('Reset Password');
		});

		return back()->with('message', 'We have e-mailed your password reset link!');
	}

	//Code For Reset_Password
	public function resetPassword(PasswordRequest $request)
	{
		$updatePassword = DB::table('password_resets')
			->where([
				'email' => $request->email,
				'token' => $request->token
			])
			->first();

		if(!$updatePassword){
			return back()->withInput()->with('error', 'Invalid token!');
		}
		$user = User::where('email', $request->email)
			->update(['password' => Hash::make($request->password)]);

		DB::table('password_resets')->where(['email'=> $request->email])->delete();

		return redirect('/login')->with('message', 'Your password has been changed!');
	}

}