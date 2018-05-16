<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Entity
{
    /**
     * @var string
     *
     * @ORM\Id()
     * @ORM\Column(type="string", length=36)
     */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
