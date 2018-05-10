<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

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
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
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
