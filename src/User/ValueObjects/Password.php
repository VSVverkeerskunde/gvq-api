<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Password
{
    const PATTERN = '/^(?=[^ ])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z])(.{8,})(?<=\S)$/';

    /**
     * @var string
     */
    private $value;

    public static function fromHash(string $hash)
    {
        $password = new Password();
        $password->setValue($hash);

        return $password;
    }

    public static function fromPlainText(string $plainTextValue)
    {
        if (!preg_match(self::PATTERN, $plainTextValue)) {
            throw new \InvalidArgumentException(
                'Invalid value for password. '.
                'Must be at least 8 characters long, contain at least one lowercase, '.
                'one uppercase and one non-alphabetical character and must not start or end with a space.'
            );
        }

        $password = new Password();
        $password->setValue(password_hash($plainTextValue, PASSWORD_DEFAULT));

        return $password;
    }

    private function setValue(string $value)
    {
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
     * @param string $plainTextValue
     * @return bool
     */
    public function verifies(string $plainTextValue): bool
    {
        return password_verify($plainTextValue, $this->toNative());
    }
}
