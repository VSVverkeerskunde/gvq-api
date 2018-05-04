<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;

abstract class AbstractDoctrineRepositoryTest extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setUp()
    {
        $configuration = Setup::createAnnotationMetadataConfiguration(
            [
                __DIR__ . '/../../../src',
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

        $this->entityManager = EntityManager::create(
            $connection,
            $configuration
        );

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($metadata);
    }
}
