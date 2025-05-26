<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;


class PasswordResetController extends Controller
{
    public function passwordResetForm()
    {
        return view('auth.passwords.email');
    }

   

public function sendResetLink(Request $request)
{
    $request->validate([
        'identifier' => 'required'
    ]);

    $identifier = $request->input('identifier');
    $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

    try {

        $count = $isEmail
            ? DB::table('users')->where('email', $identifier)->count()
            : DB::table('users')->where('phone', $identifier)->count();

        if ($count > 1) {
            return back()->withErrors([
                'identifier' => 'Sorry you cannot reset password because This ' . ($isEmail ? 'email' : 'phone number') . ' is linked to multiple accounts. Please contact support.'
            ]);
        }

        $user = $isEmail
            ? DB::table('users')->where('email', $identifier)->first()
            : DB::table('users')->where('phone', $identifier)->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'Internal Server error occured.']);
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['identifier' => 'Sorry you cannot reset password because This account is not active.']);
        }

        if ($isEmail && $user->isverifiedemail !== 'YES') {
            return back()->withErrors(['identifier' => 'Sorry you cannot reset password because this Email is not verified, Please contact support']);
        }

        if (!$isEmail && $user->isverifiedphone !== 'YES') {
            return back()->withErrors(['identifier' => 'Sorry you cannot reset password because this Phone number is not verified, Please contact support']);
        }

        // Check for non-expired existing token
        $existingToken = DB::table('password_resets')
            ->where($isEmail ? 'email' : 'phone', $identifier)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existingToken) {
            return back()->withErrors(['identifier' => 'A reset link was already sent. Please wait until it expires, usually after 5 minutes']);
        }

        // Generate and insert new token
        $token = Str::random(64);
        $now = Carbon::now();
        $expiresAt = $now->copy()->addMinutes(5);

        // Delete previous (expired) tokens
        DB::table('password_resets')->where($isEmail ? 'email' : 'phone', $identifier)->delete();

        DB::table('password_resets')->insert([
            $isEmail ? 'email' : 'phone' => $identifier,
            'token' => $token,
            'created_at' => $now,
            'expires_at' => $expiresAt,
            'updated_at' => $now
        ]);

        $resetUrl = route('password.reset.form', ['token' => $token]);

        if ($isEmail) {
            // Send Email
            $data = [
                'name' => $user->firstname,
                'time' => now()->format('Y-m-d H:i:s'),
                'ip' => $request->ip(),
                'platform' => $request->header('User-Agent'),
                'device' => 'Web',
                'city' => 'Unknown',
                'country' => 'Unknown',
                'reset_url' => $resetUrl
            ];
            Mail::to($user->email)->send(new ForgotPasswordMail($data));
        } else {
            // Send SMS
            $smsPayload = [
                "api_token" => "131|laravel_sanctum_7eazgcUJlBe7S6ax5HFw0VrnZCMNsaJ2jQzn2TAnfeb87ff8",
                "recipient" => "255" . ltrim($user->phone, '0'),
                "sender_id" => "TAARIFA",
                "type" => "plain",
                "message" => "Hi $user->firstname, reset your password here: $resetUrl"
            ];

            $response = Http::timeout(30) // wait up to 30 seconds for a response
                ->retry(3, 1000) // retry up to 3 times with 1 second (1000 ms) delay between attempts
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://sms.webline.co.tz/api/http/sms/send', $smsPayload);


            if (!$response->successful()) {
                Log::error('SMS API Failed Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json(),
                ]);

                return back()->withErrors(['identifier' => 'Failed to send SMS, please try again.']);
            } else {
                // Debug: log successful response too
                Log::info('SMS API Successful Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json(),
                ]);
            }

        }

        $this->logAction($request, 'success', $user->id, 'password reset request', 'request_password_reset');

        return back()->with('status', 'Password reset link has been sent.');
    } catch (\Exception $e) {
        Log::error('Password Reset Error: ' . $e->getMessage());
        return back()->withErrors(['identifier' => 'Something went wrong. Please try again.']);
    }
}


    public function resetForm($token)
    {
        $passwordReset = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$passwordReset) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Invalid request.']);
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_resets')->where('token', $token)->delete();
            return redirect()->route('login')
                ->withErrors(['email' => 'Password reset link has expired. Please request a new one.']);
        }

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $passwordReset->email,
            'phone' => $passwordReset->phone,
        ]);
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
        ],
        'token' => 'required'
    ]);

    // Get the record from password_resets by token
    $resetRecord = DB::table('password_resets')
        ->where('token', $request->token)
        ->first();

    if (!$resetRecord) {
        return back()->withErrors(['token' => 'Invalid or expired token!']);
    }

    // Check if the reset link is expired (older than 24 hours)
    if (Carbon::parse($resetRecord->created_at)->addHours(24)->isPast()) {
        DB::table('password_resets')->where('token', $request->token)->delete();
        return back()->withErrors(['token' => 'Password reset link is expired!']);
    }

    // Try to reset by email or phone
    $user = User::where('email', $resetRecord->email)
                ->orWhere('phone', $resetRecord->email) // assuming phone was stored in `email` column
                ->first();

    if (!$user) {
        return back()->withErrors(['token' => 'No user found for this reset request.']);
    }

    // Update the user password
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    // Delete the reset record
    DB::table('password_resets')->where('token', $request->token)->delete();

    return redirect()->route('login')->with('status', 'Your password has been changed!');
}


    protected function logAction(
        Request $request,
        string $status = 'failed',
        ?int $userId = null,
        ?string $note = null,
        ?string $action = null
    ) {
        $uid = Str::random(32);
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        $ipAddress = $request->header('X-Forwarded-For') ?: $request->ip();
        $userAgent = $request->userAgent();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $deviceType = $agent->isMobile() ? 'mobile' : 'desktop';
        $requestHeaders = json_encode($request->headers->all());

        $logId = DB::table('audit_logs_login_attempts')->insertGetId([
            'uid' => $uid,
            'user_id' => $userId,
            'status' => $status,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'browser' => $browser,
            'platform' => $platform,
            'device_type' => $deviceType,
            'request_headers' => $requestHeaders,
            'action' => $action,
            'source' => $note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $log = DB::table('audit_logs_login_attempts')->where('id', $logId)->first();

        dispatch(new \App\Jobs\FetchIpIntelligenceJob($log, 'audit_logs_login_attempts'));
    }
}