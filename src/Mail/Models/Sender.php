<?php declare(strict_types=1);

namespace VSV\GVQ_API\Mail\Models;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\User\ValueObjects\Email;

class Sender
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var NotEmptyString
     */
    private $name;

    /**
     * @var Language
     */
    private $language;

    /**
     * @param Email $email
     * @param NotEmptyString $name
     * @param Language $language
     */
    public function __construct(
        Email $email,
        NotEmptyString $name,
        Language $language
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->language = $language;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return NotEmptyString
     */
    public function getName(): NotEmptyString
    {
        return $this->name;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }
}
