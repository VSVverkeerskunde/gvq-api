<?php declare(strict_types=1);

namespace VSV\GVQ_API\Common\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

abstract class AbstractDoctrineRepositoryTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp(): void
    {
        $configuration = Setup::createAnnotationMetadataConfiguration(
            [
                __DIR__.'/../../../src/Question/Repositories/Entities',
                __DIR__.'/../../../src/User/Repositories/Entities',
                __DIR__.'/../../../src/Company/Repositories/Entities',
                __DIR__.'/../../../src/Registration/Repositories/Entities',
                __DIR__.'/../../../src/Quiz/EventStore',
            ],
            true,
            null,
            null,
            false
        );

        $connection = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        /** @var  entityManager */
        $this->entityManager = EntityManager::create(
            $connection,
            $configuration
        );

        $this->objectRepository = $this->entityManager->getRepository(
            $this->getRepositoryName()
        );

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($metadata);
    }

    abstract protected function getRepositoryName(): string;
}
