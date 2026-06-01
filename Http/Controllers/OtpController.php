<?php

namespace Modules\NexcoreClientManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    // GET /nexcore/otp - show OTP verification page
    public function show(Request $request)
    {
        // Generate 6-digit OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store in session with 5 min expiry timestamp
        session(['nexcore_otp' => $otp, 'nexcore_otp_expires' => time() + 300]);

        // Get the logged-in user's phone (if available) for display
        $user = Auth::user();
        $phone = '';
        if ($user && isset($user->phone)) {
            $phone = $user->phone;
        }

        return view('pages.authentication.otp', [
            'otp_code' => $otp,  // Temporary - show on screen until SMS connected
            'phone' => $phone,
            'user' => $user,
        ]);
    }

    // POST /nexcore/otp/verify - verify the OTP
    public function verify(Request $request)
    {
        $submitted = $request->input('otp_code');
        $stored = session('nexcore_otp');
        $expires = session('nexcore_otp_expires', 0);

        // Check expiry
        if (time() > $expires) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'OTP has expired. Please request a new code.'], 422);
            }
            return redirect()->route('nexcore.otp.show')->with('error', 'OTP expired');
        }

        // Check match
        if ($submitted !== $stored) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Invalid verification code. Please try again.'], 422);
            }
            return redirect()->route('nexcore.otp.show')->with('error', 'Invalid OTP');
        }

        // OTP verified - clear session
        session()->forget(['nexcore_otp', 'nexcore_otp_expires']);
        session(['nexcore_otp_verified' => true]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'redirect_url' => url('nexcore/clients')]);
        }

        return redirect('nexcore/clients');
    }

    // POST /nexcore/otp/resend - generate new OTP
    public function resend(Request $request)
    {
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        session(['nexcore_otp' => $otp, 'nexcore_otp_expires' => time() + 300]);

        return response()->json([
            'success' => true,
            'message' => 'New verification code sent.',
            'otp_code' => $otp  // Temporary - remove when SMS connected
        ]);
    }
}
