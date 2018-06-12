<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Password
{
    /**
     * @var string
     */
    private $value;

    private function __construct()
    {
    }

    /**
     * @param string $hash
     * @return Password
     */
    public static function fromHash(string $hash): Password
    {
        $password = new Password();
        $password->setValue($hash);

        return $password;
    }

    /**
     * @param string $plainTextValue
     * @return Password
     */
    public static function fromPlainText(string $plainTextValue): Password
    {
        if (!preg_match('/^(?=[^ ])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z])(.{8,})(?<=\S)$/', $plainTextValue)) {
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

    /**
     * @param string $value
     */
    private function setValue(string $value): void
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
