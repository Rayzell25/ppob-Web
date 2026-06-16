<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class WAAuthController extends Controller
{
    public function showForgotForm() 
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request) 
    {
        $input = $request->input('identifier');
        if (!$input) return back()->withErrors(['identifier' => 'Input kosong!']);

        $user = User::where('email', $input)->orWhere('phone', $input)->first();
        if (!$user) return back()->withErrors(['identifier' => 'Data tidak ditemukan!']);

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email], 
            ['token' => $token, 'created_at' => now()]
        );

        try {
            Mail::raw('Link Reset: ' . url('/password/reset/' . $token), function ($message) use ($user) {
                $message->to($user->email)->subject('Reset Password');
            });
            return back()->with('success', 'Email berhasil dikirim!');
        } catch (\Exception $e) {
            Log::error('SMTP ERROR: ' . $e->getMessage());
            return back()->withErrors(['identifier' => 'Gagal kirim email: ' . $e->getMessage()]);
        }
    }
}
