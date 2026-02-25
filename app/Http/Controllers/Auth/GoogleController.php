<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'id_google' => $googleUser->getId(),
                'password' => Hash::make(Str::random(24)), 
            ]);

            $otp = strtoupper(Str::random(6));
            $user->otp = $otp;
            $user->save(); 

            session(['otp_user_id' => $user->id]);

            try {
                Mail::raw("Kode OTP Anda: $otp", function ($message) use ($user) {
                    $message->to($user->email)->subject('Login Verification Code');
                });
            } catch (\Exception $e) {
                Log::error("Gagal kirim email ke {$user->email}: " . $e->getMessage());
            }

            Log::info("OTP TERBARU untuk {$user->email}: {$otp}");

            return redirect()->route('otp.view');

        } catch (\Exception $e) {
            Log::error("Google Error Total: " . $e->getMessage());
            return redirect('/')->with('error', 'Gagal login via Google.');
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);
        
        $userId = session('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect('/')->with('error', 'Sesi habis, silakan login ulang.');
        }

        // Cek kecocokan OTP
        if ($user->otp === strtoupper($request->otp)) {
            // Hapus OTP setelah sukses
            $user->otp = null;
            $user->save();
            
            Auth::login($user);
            session()->forget('otp_user_id');

            return redirect()->to('/Dashboard'); 
        }

        return back()->with('error', 'Kode OTP salah!');
    }
}