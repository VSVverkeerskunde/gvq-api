<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Email
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
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
