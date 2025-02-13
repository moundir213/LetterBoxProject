<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * Service responsible for handling email verification.
 */
class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Sends a verification email to the user with a unique signed URL.
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        // Generate a signed URL for email verification
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        // Add the verification details to the email context
        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        // Send the email
        $this->mailer->send($email);
    }

    /**
     * Validates the email confirmation request and marks the user as verified.
     * 
     * @throws VerifyEmailExceptionInterface if verification fails.
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        // Validate the email confirmation request
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

        // Mark the user as verified
        $user->setIsVerified(true);

        // Persist the updated user entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
