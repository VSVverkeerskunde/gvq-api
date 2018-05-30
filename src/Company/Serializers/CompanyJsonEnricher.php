<?php declare(strict_types=1);

namespace VSV\GVQ_API\Company\Serializers;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;

class CompanyJsonEnricher implements JsonEnricher
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
        $companyAsArray = json_decode($json, true);

        $companyAsArray['id'] = $this->uuidFactory->uuid4()->toString();

        for ($index = 0; $index < count($companyAsArray['aliases']); $index++) {
            $companyAsArray['aliases'][$index]['id'] = $this->uuidFactory->uuid4()->toString();
        }

        return json_encode($companyAsArray);
    }
}
