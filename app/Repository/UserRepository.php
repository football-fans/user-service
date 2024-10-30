<?php

namespace App\Repository;

use App\Enums\VerificationType;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByIdentifier($identifier) :? User
    {
        return User::where('email', '=', $identifier)
            ->orWhere('phone', '=', $identifier)
            ->first();
    }
    public function login($data)
    {
        $user = $this->findByIdentifier($data['identifier']);
        if ($user && Auth::attempt(['id' => $user->id, 'password' => $data['password']])) return $this->getToken($user);

        return null;
    }
    public function create(Verification $verification)
    {
        $user = new User();
        if ($verification->type = VerificationType::EMAIL->value) {
            $user->email = $verification->identifier;
            $user->email_verified_at = Carbon::now();
        } else {
            $user->phone = $verification->identifier;
            $user->phone_verified_at = Carbon::now();
        }
        $user->name = $verification->name;
        $user->password = $verification->password;
        $user->save();

        return $user;
    }

    public function newPassword(User $user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    public function generatePassword(int $length = 12, bool $includeSpecialChars = true): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        if ($includeSpecialChars) {
            $characters .= '!@#$%^&*()-_=+[]{}<>?';
        }

        $password = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $maxIndex)];
        }

        return $password;
    }

    public function getToken(User $user)
    {
        return $user->createToken('YourAppName')->plainTextToken;
    }

    public function getPasswordResetToken(User $user, $identifier)
    {
        return $user->passwordResetTokens()->where('identifier', $identifier)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }
    public function createOrGetPasswordResetToken(User $user, $identifier)
    {
        $passwordResetToken = $this->getPasswordResetToken($user, $identifier);
        if (!$passwordResetToken) {
            $passwordResetToken = new PasswordResetToken();
            $passwordResetToken->tokenable_type = get_class($user);
            $passwordResetToken->tokenable_id = $user->id;
            $passwordResetToken->token = $user->generateTokenString();
            $passwordResetToken->identifier = $identifier;
            $passwordResetToken->expires_at = Carbon::now()->addMinutes(10);
            $passwordResetToken->save();
        }

        return $passwordResetToken;
    }

    public function updatePassword(User $user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
