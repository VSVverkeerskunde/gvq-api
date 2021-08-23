<?php

namespace VSV\GVQ_API\Contest;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Contest\Models\ContestParticipation;

class ContestParticipationCsvData
{
    /**
     * @var \VSV\GVQ_API\Contest\Models\ContestParticipation[]
     */
    private $contestParticipations;

    /**
     * @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface
     */
    private $normalizer;

    /**
     * @var string[]
     */
    private $headers;

    public function __construct(iterable $contestParticipations, NormalizerInterface $normalizer)
    {
        $this->contestParticipations = $contestParticipations;
        $this->normalizer = $normalizer;

        $this->headers = [
            'id',
            'language',
            'channel',
            'contestParticipant.email',
            'contestParticipant.firstName',
            'contestParticipant.lastName',
            'contestParticipant.dateOfBirth',
            'answer1',
            'answer2',
            'gdpr1',
            'gdpr2',
            'association'
        ];
    }

    public function rows(): iterable
    {
        yield $this->headers;

        foreach ($this->contestParticipations as $contestParticipation) {
            yield $this->getValues($contestParticipation);
        }
    }

    private function getValues(ContestParticipation $contestParticipation) {
        $data = $this->normalizer->normalize($contestParticipation);

        $values = [];

        $this->flatten($data, $values);

        // The values returned by the serializer might be either more or less
        // than the columns we want, so limit or expand the values to the
        // headers we have explicitly set.
        return $this->limitValuesToHeaders($values);
    }

    private function limitValuesToHeaders($values): array
    {
        $headers = array_fill_keys($this->headers, '');
        $replaced = array_replace($headers, $values);
        return array_intersect_key($replaced, $headers);
    }

    /**
     * Flattens an array and generates keys including the path.
     */
    private function flatten(
        array $array,
        array &$result,
        string $keySeparator = '.',
        string $parentKey = ''
    ) {
        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $this->flatten($value, $result, $keySeparator, $parentKey.$key.$keySeparator);
            } else {
                $result[$parentKey.$key] = $value;
            }
        }
    }
}