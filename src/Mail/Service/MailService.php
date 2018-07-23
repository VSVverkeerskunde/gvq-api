<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Service;

use VSV\GVQ_API\Registration\Models\Registration;

interface MailService
{
    /**
     * @param Registration $registration
     */
    public function sendPasswordRequestMail(Registration $registration): void;

    /**
     * @param Registration $registration
     */
    public function sendActivationMail(Registration $registration): void;

    /**
     * @param Registration $registration
     */
    public function sendWelcomeMail(Registration $registration): void;
}
