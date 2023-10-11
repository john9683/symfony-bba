<?php

namespace App\Service;

use App\Entity\User;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class VerifyEmail
{
    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
    }

    /**
     * @param string $route
     * @param User $user
     * @return string
     */
    public function getVerifyEmailLink(string $route, User $user): string
    {
        return $this->verifyEmailHelper->generateSignature(
            $route,
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        )->getSignedUrl();
    }
}