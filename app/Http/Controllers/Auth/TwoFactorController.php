<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Send2faOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FALaravel\Support\Authenticator;



use Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;




class TwoFactorController extends Controller
{
    public function showMethodSelection()
    {
        // Get the authenticated user
        $user = DB::table('users')
            ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select(
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.email',
                'users.phone',
                'users.gender',
                'users.photo',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'users.two_factor_status',
                'workspaces.display_name as workspace_name',
                'users.two_factor_method',
                'users.status' // Assuming 1 means 2FA is enabled
            )
            ->where('users.id', Auth::id())
            ->first();

        // Check if user has already enabled 2FA (status == 1)
        if ($user && $user->two_factor_status == 1 && $user->two_factor_method) {
            // If 2FA is already enabled, redirect to success page
            return redirect()->route('twofa.success')
                ->with('message', 'You have already set up Two-Factor Authentication. You cannot set up a new method until you terminate the existing one.');
        }

        // Get the campuses associated with the user
        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', Auth::id())
            ->pluck('base_campuses.name');

        // Default fallback image path (relative to public/)
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        // Try to load and encode user's profile photo
        if ($user && $user->photo) {
            $photoPath = ltrim($user->photo, '/'); // Just "uploads/photos/users/xxxx.png"
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }
        } else {
            // No photo set
            $user->photo_base64 = asset($defaultImagePath);
        }

        return view('base.2fa.select-method', compact('user', 'campuses'));
    }


    public function submitMethodSelection(Request $request)
    {
        $request->validate([
            'method' => 'required|in:email,google',
        ]);

        $user = Auth::user();

        // Check if 2FA is already enabled
        if ($user->two_factor_method && $user->two_factor_status == 1) {
            return redirect()->route('twofa.success')->with('message', 'You have already set up Two-Factor Authentication. Please remove it before setting up a new method.');
        }

        session(['2fa_method' => $request->method]);

        if ($request->method === 'email') {
            $request->validate([
                'email' => 'required|email',
            ]);

            if ($request->email !== $user->email) {
                return back()->withErrors(['email' => 'This email does not belong to your account.']);
            }

            // Generate OTP
            $otp = rand(100000, 999999);

            // Insert OTP into database using Query Builder
            DB::table('users_otps')->insert([
                'user_id'    => $user->id,
                'otp'        => $otp,
                'type'       => 'email_verification',
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send OTP mail
            Mail::to($user->email)->send(new \App\Mail\Send2faOtpMail($otp, $user->firstname));

            session(['2fa_otp_pending' => true]);

            return redirect()->route('twofa.verify-otp');
        }

        return redirect('/two-auth/google');
    }



    



    public function showVerifyOtpForm()
    {
        if (!session('2fa_otp_pending')) {
            return redirect()->route('twofa.select-method')->with('error', 'Access denied.');
        }

        $user = DB::table('users')
            ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select(
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.email',
                'users.phone',
                'users.gender',
                'users.photo',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->where('users.id', Auth::id())
            ->first();

        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', Auth::id())
            ->pluck('base_campuses.name');

        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        if ($user && $user->photo) {
            $photoPath = ltrim($user->photo, '/');
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }
        } else {
            $user->photo_base64 = asset($defaultImagePath);
        }

        return view('base.2fa.verify-email-otp', compact('user', 'campuses'));
    }


    public function verifyEmailOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $userId = auth()->id();
        $otpRecord = DB::table('users_otps')
            ->where('user_id', $userId)
            ->where('otp', $request->otp)
            ->where('type', 'email_verification')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Mark OTP as used
        DB::table('users_otps')
            ->where('id', $otpRecord->id)
            ->update(['status' => 'used', 'updated_at' => now()]);

        // Update user's two_factor_method
        DB::table('users')
            ->where('id', $userId)
            ->update([
                'two_factor_method' => 'email',
                'two_factor_status' => 1,
                'updated_at' => now()
            ]);


        session()->forget('2fa_otp_pending');

        return redirect('/two-auth/success');
    }



    public function showSuccess()
    {
        $user = DB::table('users')
            ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
            ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
            ->select(
                'users.id',
                'users.firstname',
                'users.middlename',
                'users.lastname',
                'users.username',
                'users.email',
                'users.phone',
                'users.gender',
                'users.photo',
                'users.two_factor_method',
                'users.two_factor_status',
                'users.status',
                'base_campuses.name as campus_name',
                'user_roles.name as role_name',
                'workspaces.display_name as workspace_name'
            )
            ->where('users.id', Auth::id())
            ->first();

        // If 2FA is not enabled, redirect back with message
        if (!$user || $user->two_factor_status != 1 || !$user->two_factor_method) {
            return redirect()->route('twofa.select-method')
                ->with('error', 'You have not completed 2FA setup yet.');
        }

        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', $user->id)
            ->pluck('base_campuses.name');

        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        if ($user->photo) {
            $photoPath = ltrim($user->photo, '/');
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }
        } else {
            $user->photo_base64 = asset($defaultImagePath);
        }

        return view('base.2fa.success', compact('user', 'campuses'));
    }



    public function deactivate(Request $request)
    {
        $userId = Auth::id();

        DB::table('users')
            ->where('id', $userId)
            ->update([
                'two_factor_method' => null,
                'two_factor_status' => 0,
                'updated_at' => now(),
            ]);
        //redirect to select route
        return redirect()->route('twofa.select-method')->with('success', 'Two-factor authentication has been deactivated.');
    }





    public function showGoogleSetup(Request $request)
    {

        // Get the authenticated user
        $user = DB::table('users')
        ->join('workspaces', 'users.default_workspace', '=', 'workspaces.id')
        ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
        ->leftJoin('base_campuses', 'users.campus_id', '=', 'base_campuses.id')
        ->select(
            'users.firstname',
            'users.middlename',
            'users.lastname',
            'users.username',
            'users.email',
            'users.phone',
            'users.gender',
            'users.photo',
            'base_campuses.name as campus_name',
            'user_roles.name as role_name',
            'users.two_factor_status',
            'workspaces.display_name as workspace_name',
            'users.two_factor_method',
            'users.status' // Assuming 1 means 2FA is enabled
        )
        ->where('users.id', Auth::id())
        ->first();

        // Check if user has already enabled 2FA (status == 1)
        if ($user && $user->two_factor_status == 1 && $user->two_factor_method) {
            // If 2FA is already enabled, redirect to success page
            return redirect()->route('twofa.success')
                ->with('message', 'You have already set up Two-Factor Authentication. You cannot set up a new method until you terminate the existing one.');
        }

        // Get the campuses associated with the user
        $campuses = DB::table('user_campuses')
            ->join('base_campuses', 'user_campuses.campus_id', '=', 'base_campuses.id')
            ->where('user_campuses.user_id', Auth::id())
            ->pluck('base_campuses.name');

        // Default fallback image path (relative to public/)
        $defaultImagePath = 'assets/images/users/avatar-1.jpg';

        // Try to load and encode user's profile photo
        if ($user && $user->photo) {
            $photoPath = ltrim($user->photo, '/'); // Just "uploads/photos/users/xxxx.png"
            if (Storage::disk('public')->exists($photoPath)) {
                $fileContents = Storage::disk('public')->get($photoPath);
                $mimeType = Storage::disk('public')->mimeType($photoPath);
                $user->photo_base64 = 'data:' . $mimeType . ';base64,' . base64_encode($fileContents);
            } else {
                $user->photo_base64 = asset($defaultImagePath);
            }
        } else {
            // No photo set
            $user->photo_base64 = asset($defaultImagePath);
        }





        // Get the authenticated user
        $user = Auth::user();

        // Initialize Google2FA instance
        $google2fa = app('pragmarx.google2fa');

        // Generate a new secret key for the user
        $secret = $google2fa->generateSecretKey();

        // Store the secret key in the user's record for future validation
        DB::table('users')
            ->where('id', $user->id)
            ->update(['google2fa_secret' => $secret]);

        // Generate the QR Code URL for Google Authenticator
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),  // App Name (e.g., "MyApp")
            $user->email,        // User's email (or any identifier for the user)
            $secret              // The secret key
        );

        // Use BaconQrCode to render the image inline
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);  // Generate the SVG QR code

        // Return the view and pass necessary data (user, QR code, secret)
        return view('base.2fa.google', compact('user', 'qrCodeSvg', 'secret', 'campuses'));
    }




    


    public function verifyGoogleOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Initialize Google2FA instance
        $google2fa = app('pragmarx.google2fa');

        // Get the stored secret key from the database
        $secret = $user->google2fa_secret;

        // Verify the OTP entered by the user
        $valid = $google2fa->verifyKey($secret, $request->otp);

        if ($valid) {
            // Update user's 2FA status to enabled
            DB::table('users')
                ->where('id', $user->id)
                ->update(['two_factor_method' => 'google', 'two_factor_status' => 1]);

            return redirect()->route('twofa.success');
        }

        return back()->withErrors(['otp' => 'Invalid OTP, please try again.']);
    }



}

