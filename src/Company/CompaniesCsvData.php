<?php

namespace VSV\GVQ_API\Company;

use function array_replace;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Common\CsvData;
use VSV\GVQ_API\Company\Models\Company;

class CompaniesCsvData implements CsvData
{
    /**
     * @var \VSV\GVQ_API\Company\Models\Company[]
     */
    private $companies;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var string[]
     */
    private $headers;

    public function __construct(iterable $companies, NormalizerInterface $normalizer)
    {
        $this->companies = $companies;
        $this->normalizer = $normalizer;

        $this->headers = [
            'id',
            'name',
            'numberOfEmployees',
            'aliases.0.language',
            'aliases.0.alias',
            'aliases.1.language',
            'aliases.1.alias',
            'user.email',
            'user.firstName',
            'user.lastName',
            'user.language',
            'nrOfPassedEmployees'
        ];
    }

    public function rows(): iterable
    {
        yield $this->headers;

        foreach ($this->companies as $company) {
           yield $this->getValues($company);
        }
    }

    private function getValues(Company $company) {
        $data = $this->normalizer->normalize($company);

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
