<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Models\UserProfile;
use App\Services\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected Logger $logger;
//l
    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /** ------------------------------------------
     * Show Profile Page
     * ------------------------------------------ */
    public function show(): View
    {
        $this->logger->info('Profile page accessed', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
        ]);

        return view('myauth.profile', ['user' => Auth::user()]);
    }


    /** ------------------------------------------
     * Update profile (except password)
     * ------------------------------------------ */
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Update text fields
        $user->update($request->only([
            'name', 'email', 'phone', 'birth_date', 'gender', 'location'
        ]));

        // Update profile image
        $this->handleProfileImage($request, $user);

        $user->load('profile');

        $this->logger->info('Profile updated', [
            'user_id' => $user->id
        ]);

        return $this->jsonOrRedirect(
            message: 'Profile updated successfully!',
            data: ['profile_image' => $user->profile_image_url]
        );
    }


    /** ------------------------------------------
     * Update Password
     * ------------------------------------------ */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();

        // Check current password
        if (! $this->checkCurrentPassword($request->current_password)) {
            return $this->invalid(
                field: 'current_password',
                message: 'Current password is incorrect.'
            );
        }

        // Hash + update new password
        $user->password = Hash::make($this->hmac($request->password));
        $user->save();

        // Email notification
        $token = Password::createToken($user);
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        Mail::send('emails.password_changed', [
            'user' => $user,
            'resetUrl' => $resetUrl
        ], function ($message) use ($user) {
            $message->to($user->email)->subject('Your password has been changed');
        });

        $this->logger->info('Password updated', ['user_id' => $user->id]);

        return $this->jsonOrRedirect('Password updated successfully!');
    }


    /** ------------------------------------------
     * Delete profile picture
     * ------------------------------------------ */
    public function deletePic()
    {
        $user = Auth::user();
        $profile = $user->profile;

        if ($profile && $profile->profile_image) {
            $path = public_path('images/profile/' . $profile->profile_image);
            if (File::exists($path)) File::delete($path);

            $profile->update(['profile_image' => null]);
        }

        $this->logger->info('Profile picture deleted', ['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'has_profile_image' => false,
            'message' => 'Profile picture deleted!'
        ]);
    }


    /** ------------------------------------------
     * AJAX: Validate current password
     * ------------------------------------------ */
    public function checkPassword()
    {
        $isValid = $this->checkCurrentPassword(request('current_password'));

        return response()->json([
            'valid' => $isValid,
            'message' => $isValid ? 'Password is correct' : 'Password is incorrect'
        ]);
    }


    /* ===============================================================
     *  HELPERS
     * =============================================================== */

    private function hmac(string $password): string
{
    return hash_hmac('sha256', $password, env('PASSWORD_HMAC_KEY'));
}

    private function checkCurrentPassword($input): bool
{
    if (! $input) return false;

    // ✅ Apply same HMAC + bcrypt check
    return Hash::check($this->hmac($input), Auth::user()->password);
}

    private function handleProfileImage($request, $user)
    {
        if (! $request->hasFile('profile_image')) return;

        $profile = $user->profile ?? new UserProfile(['user_id' => $user->id]);
        $path = public_path('images/profile');

        if (! File::exists($path)) File::makeDirectory($path, 0755, true);

        // Delete old image
        if ($profile->profile_image && File::exists("$path/{$profile->profile_image}")) {
            File::delete("$path/{$profile->profile_image}");
        }

        // Save new image
        $filename = uniqid('profile_', true) . '.' . $request->file('profile_image')->extension();
        $request->file('profile_image')->move($path, $filename);

        $profile->profile_image = $filename;

        $profile->exists
            ? $profile->save()
            : $user->profile()->save($profile);
    }


    /** Unified JSON or redirect response */
    private function jsonOrRedirect(string $message, array $data = [])
    {
        return request()->ajax()
            ? response()->json(['success' => true, 'message' => $message] + $data)
            : back()->with('success', $message);
    }

    private function invalid(string $field, string $message)
    {
        $error = [$field => [$message]];

        return request()->ajax()
            ? response()->json(['errors' => $error], 422)
            : back()->withErrors($error);
    }
}
