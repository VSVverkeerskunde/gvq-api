<?php

namespace VSV\GVQ_API\Company;

use function array_replace;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Company\Models\Company;
use VSV\GVQ_API\Statistics\Repositories\CompanyPlayedQuizzesRepository;
use VSV\GVQ_API\Statistics\Repositories\EmployeeParticipationRepository;
use VSV\GVQ_API\Statistics\Repositories\TopScoreRepository;

class CompaniesCsvData
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
     * @var CompanyPlayedQuizzesRepository
     */
    private $companyPlayedQuizzesRepository;

    /**
     * @var string[]
     */
    private $headers;

    public function __construct(
        iterable $companies,
        CompanyPlayedQuizzesRepository $companyPlayedQuizzesRepository,
        EmployeeParticipationRepository $employeeParticipationRepository,
        TopScoreRepository $topScoreRepository,
        NormalizerInterface $normalizer
    ) {
        $this->companies = $companies;
        $this->normalizer = $normalizer;

        $this->companyPlayedQuizzesRepository = $companyPlayedQuizzesRepository;
        $this->employeeParticipationRepository = $employeeParticipationRepository;
        $this->topScoreRepository = $topScoreRepository;

        $this->headers = [
            'id',
            'name',
            'numberOfEmployees',
            'type',
            'aliases.0.language',
            'aliases.0.alias',
            'aliases.1.language',
            'aliases.1.alias',
            'user.email',
            'user.firstName',
            'user.lastName',
            'user.language',
            'nrOfPassedEmployees',
            'average_topscore',
            'unique_participants',
            'unique_participants.nl',
            'unique_participants.fr',
            'played_quizzes.nl',
            'played_quizzes.fr',
        ];
    }

    public function rows(): iterable
    {
        yield $this->headers;

        foreach ($this->companies as $company) {
            yield $this->getValues($company);
        }
    }

    private function getValues(Company $company)
    {
        $data = $this->normalizer->normalize($company);

        $values = [];

        $this->flatten($data, $values);

        $values['average_topscore'] = number_format(
            $this->topScoreRepository->getAverageForCompany($company->getId())->toNative(),
            2,
            ',',
            NULL
        );

        $values['unique_participants'] = $this->employeeParticipationRepository->countByCompany($company->getId())
            ->toNative();
        $nl = new Language('nl');
        $values['unique_participants.nl'] = $this->employeeParticipationRepository->countByCompanyAndLanguage(
            $company->getId(),
            $nl
        );
        $fr = new Language('fr');
        $values['unique_participants.fr'] = $this->employeeParticipationRepository->countByCompanyAndLanguage(
            $company->getId(),
            $fr
        );

        // Augment company data with count of played quizzes in each language.
        $values['played_quizzes.nl'] = $this->companyPlayedQuizzesRepository->getCount($company->getId(),
            $nl);
        $values['played_quizzes.fr'] = $this->companyPlayedQuizzesRepository->getCount($company->getId(),
            $fr);

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
                $this->flatten($value, $result, $keySeparator,
                    $parentKey . $key . $keySeparator);
            } else {
                $result[$parentKey . $key] = $value;
            }
        }
    }
}
