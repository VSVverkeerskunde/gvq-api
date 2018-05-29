<?php declare(strict_types=1);

namespace VSV\GVQ_API\User\ValueObjects;

class Role
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string[]
     */
    private $allowedValues = [
        'admin',
        'vsv',
        'contact',
    ];

    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        if (!in_array($value, $this->allowedValues)) {
            throw new \InvalidArgumentException('Invalid value: '.$value.' for role.');
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
     * @param Role $role
     * @return bool
     */
    public function equals(Role $role): bool
    {
        return $this->toNative() === $role->toNative();
    }
}
