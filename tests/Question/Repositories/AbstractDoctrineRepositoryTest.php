<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Repositories;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Question\Repositories\Mappings\UriType;

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
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function setUp(): void
    {
        $configuration = Setup::createConfiguration(true);
        $simplifiedYmlDriver = new SimplifiedYamlDriver(
            [
                __DIR__.'/../../../src/Question/Repositories/Mappings' => 'VSV\GVQ_API',
            ]
        );
        $configuration->setMetadataDriverImpl($simplifiedYmlDriver);

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

        if (!Type::hasType('ramsey_uuid')) {
            Type::addType('ramsey_uuid', UuidType::class);
        }

        if (!Type::hasType('league_uri')) {
            Type::addType('league_uri', UriType::class);
        }

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($metadata);
    }

    abstract protected function getRepositoryName(): string;

    /**
     * @param mixed $entity
     */
    protected function save($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        // Make sure to detach the saved entity.
        $this->entityManager->clear();
    }

    /**
     * @param UuidInterface $id
     * @return mixed
     */
    protected function findById(UuidInterface $id)
    {
        // Make sure to detach the entity to search
        $this->entityManager->clear();

        $entity = $this->objectRepository->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $entity;
    }
}
