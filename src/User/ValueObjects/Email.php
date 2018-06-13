<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Email
{
    const PATTERN = '/^[a-zA-Z0-9_+&*-]+(?:\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,7}$/';

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!preg_match(self::PATTERN, $value)) {
            throw new \InvalidArgumentException('Invalid value '.$value.' for email');
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->value;
    }

    /**
     * @param Email $email
     * @return bool
     */
    public function equals(Email $email): bool
    {
        return $this->toNative() === $email->toNative();
    }
}
