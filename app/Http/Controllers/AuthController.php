<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Verified;
use App\Mail\SendingVerificationCode;
use App\Mail\SendinfVeCodeForPassword;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profileImg' => 'required',
            'profileImg.*' => 'required|image|mimes:jpeg,png,jpg,gif|',
            'address.street' => 'required|string|max:255', // street
            'address.latitude' => 'required|numeric',     // latitude
            'address.longitude' => 'required|numeric',    // longitude
        ]);

        $image = $request->file('profileImg');
        $filename = 'users/' . $image[0]->getClientOriginalName();
        
        // Store with public visibility
        Storage::disk('s3')->put($filename, file_get_contents($image[0]), 'public');
        $url = Storage::disk('s3')->url($filename);
        if (empty($url)) {
            throw new \Exception("Failed to generate URL for file: $filename");
        }

        $addressData = [
            'street' => $validated['address']['street'],
            'latitude' => $validated['address']['latitude'],
            'longitude' => $validated['address']['longitude']
        ];
    
        // Store the address as a JSON string
        $addressString = json_encode($addressData);
    
        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'location' => $addressString,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_picture' => $url,
        ]);

        self::sendVerificationCode($user);

        return response()->json(['message' => 'User registered successfully.']);
    }

    public function sendVerificationCode($user){
        // Generate the verification code: 4 digits user ID (padded) + 4 random digits
        $userIdPadded = str_pad($user->id, 4, '0', STR_PAD_LEFT); // Ensure user ID is 4 digits
        $randomDigits = rand(1000, 9999); // Generate 4 random digits
        $verificationCode = $userIdPadded . $randomDigits;

        // Update the user's bio with the new verification code
        $user->bio = $verificationCode;
        $user->save();

        // Send verification code email
        Mail::to($user->email)->send(new SendingVerificationCode($verificationCode));
    }

    public function sendVerificationCodeForPasswortReset($user){
        // Generate the verification code: 4 digits user ID (padded) + 4 random digits
        $userIdPadded = str_pad($user->id, 4, '0', STR_PAD_LEFT); // Ensure user ID is 4 digits
        $randomDigits = rand(1000, 9999); // Generate 4 random digits
        $verificationCode = $userIdPadded . $randomDigits;

        // Update the user's bio with the new verification code
        $user->bio = $verificationCode;
        $user->save();

        // Send verification code email
        Mail::to($user->email)->send(new SendinfVeCodeForPassword($verificationCode));
    }

    public function resetPasswordSendVerCode($email){

        $user = User::where('email', $email)->first();

        if($user == null){
            return response()->json(['no' => ':(']);
        }

        self::sendVerificationCodeForPasswortReset($user);

        return response()->json(['success' => 'Verify your email.']);
    }

    public function checkAndVerifyBio($VerCode)
    {
        // Extract the user ID from the first 4 digits of the verification code
        $userId = (int)substr($VerCode, 0, 4); // Get the first 4 digits and remove leading zeros
    
        // Get the user associated with the extracted ID
        $user = User::find($userId);
    
        // If the user doesn't exist, return an error
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'verified' => false
            ], 404);
        }
    
        // Check if the user's bio matches the verification code
        if ($user->bio === $VerCode) {
            // If the codes match, update the email_verified_at column
            $user->email_verified_at = Carbon::now();  // Set the current timestamp
            $user->save();
    
            // Return a success message
            return response()->json([
                'message' => 'Email successfully verified.',
                'verified' => true
            ]);
        }
    
        // If codes don't match, return an error message
        return response()->json([
            'message' => 'Verification codes do not match.',
            'verified' => false
        ], 400);
    }
    
    public function verifyAndChanegPassword(Request $request)
    {
        $verCode = $request->verCode;
        $password = $request->password;
        // Extract the user ID from the first 4 digits of the verification code
        $userId = (int)substr($verCode, 0, 4); // Get the first 4 digits and remove leading zeros
    
        // Get the user associated with the extracted ID
        $user = User::find($userId);
    
        // If the user doesn't exist, return an error
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
                'success' => false
            ], 404);
        }
    
        // Check if the user's bio matches the verification code
        if ($user->bio === $verCode) {

            // If the codes match, update the email_verified_at column
            $user->password = Hash::make($password);
            $user->save();
    
            // Return a success message
            return response()->json([
                'message' => 'Verification successfully.',
                'success' => true
            ]);
        }
    
        // If codes don't match, return an error message
        return response()->json([
            'message' => 'Verification codes do not match.',
            'success' => false
        ], 400);
    }


    // Register new user
    // public function register(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'city' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:8',
    //     ]);

    //     $codeUser = rand(1000, 9999); 
    //     $codeServer = rand(1000, 9999); 

    //     $user = User::create([
    //         'name' => $validated['name'],
    //         'city' => $validated['city'],
    //         'email' => $validated['email'],
    //         "bio" =>  [$codeUser, $codeServer],
    //         'password' => Hash::make($validated['password']),
    //     ]);

    //     // $user->sendEmailVerificationNotification();
       
    //     Mail::to($user->email)->send(new SendingVerificationCode($code));

    //     return response()->json(['message' => 'User registered successfully.', "VerCode" =>  $codeServer]);
    // }

    // public function checkAndVerifyBio($code1, $code2)
    // {
    //     // Get the authenticated user
    //     $user = Auth::user();

    //     // Check if the 'bio' column exists and is an array
    //     if (is_array($user->bio) && count($user->bio) === 2) {
    //         // Compare the codes
    //         if (
    //             ($user->bio[0] === $code1 && $user->bio[1] === $code2) ||
    //             ($user->bio[0] === $code2 && $user->bio[1] === $code1)
    //         ) {
    //             // If the codes match, update the email_verified_at column
    //             $user->email_verified_at = Carbon::now();  // Set the current timestamp
    //             $user->save();

    //             // Optionally, return a success message or boolean
    //             return response()->json([
    //                 'message' => 'Email successfully verified.',
    //                 'verified' => true
    //             ]);
    //         }
    //     }

    //     // If codes don't match, return an error message
    //     return response()->json([
    //         'message' => 'Verification codes do not match.',
    //         'verified' => false
    //     ], 400);
    // }


    // Login user and issue token
    public function login(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find the user by email
        $user = User::where('email', $validated['email'])->first();

        // Check if the user exists and if the password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            // Return 400 for incorrect email or password
            return response()->json(['message' => 'Email or password is incorrect.'], 400);
        }

        // Check if the user has verified their email
        if (!$user->email_verified_at) {
            // Send verification email again and return 403
            self::sendVerificationCode($user);
            return response()->json(['message' => 'Please verify your email address before logging in.', "verify" => false]);
        }

        // Create token for the user
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Return 200 if login is successful
        return response()->json([
            'token' => $token,
            'user_id' => $user->id,
            'message' => 'Login successful.',
        ], 200);
    }



    // Logout user and revoke token
    public function logout()
    {
        // Get the currently authenticated user
        $user = auth()->user();
    
        // If no user is authenticated, return a response indicating the user is not logged in
        if (!$user) {
            return response()->json(['message' => 'No user authenticated'], 200);
        }
    
        // Revoke the current user's token
        $user->currentAccessToken()->delete();
    
        return response()->json(['message' => 'Logged out successfully.']);
    }
    


    public function verifyEmail(Request $request)
    {
        // Find the user by ID (the ID is included in the verification link)
        $user = User::findOrFail($request->id);

        // Check if the user has already verified their email
        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        // Mark the email as verified
        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully.']);
    }

}
