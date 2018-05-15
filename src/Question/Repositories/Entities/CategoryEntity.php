<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use VSV\GVQ_API\Question\Models\Category;
use VSV\GVQ_API\Question\ValueObjects\NotEmptyString;

/**
 * @ORM\Entity()
 * @ORM\Table(name="category")
 */
class CategoryEntity extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $name;

    /**
     * @param string $id
     * @param string $name
     */
    private function __construct(
        string $id,
        string $name
    ) {
        parent::__construct($id);
        $this->name = $name;
    }

    /**
     * @param Category $category
     * @return CategoryEntity
     */
    public static function fromCategory(Category $category): CategoryEntity
    {
        return new CategoryEntity(
            $category->getId()->toString(),
            $category->getName()->toNative()
        );
    }

    /**
     * @return Category
     */
    public function toCategory(): Category
    {
        return new Category(
            Uuid::fromString($this->getId()),
            new NotEmptyString($this->getName())
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
