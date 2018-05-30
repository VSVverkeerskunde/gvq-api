<?php declare(strict_types=1);

namespace VSV\GVQ_API\Question\Serializers;

use Ramsey\Uuid\UuidFactoryInterface;
use VSV\GVQ_API\Common\Serializers\JsonEnricher;

class QuestionJsonEnricher implements JsonEnricher
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
        $questionAsArray = json_decode($json, true);

        $questionAsArray['id'] = $this->uuidFactory->uuid4();

        for ($index = 0; $index < count($questionAsArray['answers']); $index++) {
            $questionAsArray['answers'][$index]['id'] = $this->uuidFactory->uuid4();
        }

        return json_encode($questionAsArray);
    }
}
