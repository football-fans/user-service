<?php

namespace App\Repository;

use App\Enums\VerificationType;
use App\Models\Verification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VerificationRepository
{
    public function get($type, $identifier)
    {
        return Verification::query()->where('type', $type)->where('identifier', $identifier)->first();
    }

    public function createOrGet(array $data)
    {
        $verification = $this->get($data['type'], $data['identifier']);
        if (!$verification) {
            $verification = new Verification();
            $verification->type = $data['type'];
            $verification->identifier = $data['identifier'];
        }
        $verification->token = $this->createToken();
        $verification->code = $this->createCode();
        $verification->expires_at = Carbon::now()->addMinutes(5);
        $verification->name = $data['name'] ?? null;
        $verification->password = $data['password'] ?? null;
        $verification->user_id = $data['user_id'] ?? null;
        $verification->attempts = 0;
        $verification->save();

        return $verification;
    }

    private function createToken()
    {
        return Str::uuid();
    }

    public function createCode()
    {
        return 123456;
        return rand(100000, 999999);
    }
}
