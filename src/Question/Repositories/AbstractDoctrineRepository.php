<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;

abstract class AbstractDoctrineRepository
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(
            $this->getRepositoryName()
        );
    }

    /**
     * @return string
     */
    abstract protected function getRepositoryName(): string;
}
