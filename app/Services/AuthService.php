<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Repository\VerificationRepository;
use Illuminate\Support\Carbon;

class AuthService
{
    protected $userRepository;
    protected $verificationRepository;
    protected $responseService;
    public function __construct(UserRepository $userRepository, VerificationRepository $verificationRepository, ResponseService $responseService)
    {
        $this->userRepository = $userRepository;
        $this->verificationRepository = $verificationRepository;
        $this->responseService = $responseService;
    }

    public function login($data)
    {
        $token = $this->userRepository->login($data);
        if (!$token) {
            return $this->responseService->error('Login or password invalid');
        }

        return $this->responseService->success(['token' => $token]);
    }

    public function register(array $data)
    {
        $verification = $this->verificationRepository->createOrGet($data);

        return $this->responseService->success(['token' => $verification->token],'We have sent you an activation code, please check your email.');
    }

    public function verify(array $data)
    {
        $verification = $this->verificationRepository->get($data['type'], $data['identifier']);
        if (!$verification || $verification->expires_at < Carbon::now()) {
            return $this->responseService->error('Expired activation code');
        }
        if($verification->attempts > 5) {
            return $this->responseService->error('Retry limit exceeded');
        }
        if ($verification->code != $data['code'] || $verification->token != $data['token']) {
            $verification->attempts++;
            $verification->save();

            return $this->responseService->error('Wrong code');
        }

        if ($verification->user_id) {
            $user = $verification->user;
            $passwordResetToken = $this->userRepository->createOrGetPasswordResetToken($user, $data['identifier']);
            $verification->delete();

            return $this->responseService->success(['token' => $passwordResetToken->token]);
        }
        $user = $this->userRepository->create($verification);
        $token = $this->userRepository->getToken($user);
        $verification->delete();

        return $this->responseService->success(['token' => $token]);
    }

    public function forgotPassword($data)
    {
        $user = $this->userRepository->findByIdentifier($data['identifier']);
        if (!$user) {
            return $this->responseService->error('User not found');
        }
        $data['user_id'] = $user->id;
        $verification = $this->verificationRepository->createOrGet($data);

        return $this->responseService->success(['token' => $verification->token]);
    }

    public function resetPassword($data)
    {
        $user = $this->userRepository->findByIdentifier($data['identifier']);
        if (!$user) {
            return $this->responseService->error('User not found');
        }
        $passwordResetToken = $this->userRepository->getPasswordResetToken($user, $data['identifier']);
        if (!$passwordResetToken || $passwordResetToken->token != $data['token']) {
            return $this->responseService->error('Token not found');
        }
        $this->userRepository->updatePassword($passwordResetToken->tokenable, $data['password']);
        $passwordResetToken->delete();

        return $this->responseService->success('', 'Password reset successfully.');
    }
}
