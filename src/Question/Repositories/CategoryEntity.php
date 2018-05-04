<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class CategoryEntity
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct(
        string $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param Category $category
     * @return CategoryEntity
     */
    public static function fromCategory(Category $category)
    {
        return new self(
            $category->getId()->toString(),
            $category->getName()->toNative()
        );
    }

    /**
     * @return Category
     */
    public function toCategory()
    {
        return new Category(
            Uuid::fromString($this->id),
            new NotEmptyString($this->name)
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
