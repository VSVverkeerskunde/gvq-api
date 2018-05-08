<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\DBAL\Types\Type;
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
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp()
    {
        $configuration = Setup::createYAMLMetadataConfiguration(
            [
                __DIR__ . '/../../../src/Question/Repositories/Mappings',
            ],
            true
        );

        $connection = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $this->entityManager = EntityManager::create(
            $connection,
            $configuration
        );

        if(!Type::hasType('ramsey_uuid')) {
            Type::addType('ramsey_uuid', 'Ramsey\Uuid\Doctrine\UuidType');
        }

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($metadata);
    }
}
