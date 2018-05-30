<?php declare(strict_types=1);

namespace VSV\GVQ_API\Account\Serializers;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;
use VSV\GVQ_API\User\ValueObjects\Role;

class RegistrationJsonEnricher implements JsonEnricher
{
    /**
     * @var UuidFactoryInterface
     */
    private $uuidFactory;

    /**
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(UuidFactoryInterface $uuidFactory)
    {
        $this->uuidFactory = $uuidFactory;
    }

    /**
     * @param string $json
     * @return string
     */
    public function enrich(string $json): string
    {
        $registrationAsArray = json_decode($json, true);

        $registrationAsArray['id'] = $this->uuidFactory->uuid4()->toString();

        for ($index = 0; $index < count($registrationAsArray['aliases']); $index++) {
            $registrationAsArray['aliases'][$index]['id'] = $this->uuidFactory->uuid4()->toString();
        }

        $registrationAsArray['user']['id'] = $this->uuidFactory->uuid4()->toString();
        $registrationAsArray['user']['role'] = Role::CONTACT;

        return json_encode($registrationAsArray);
    }
}
