<?php

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\QuizStarted;
use VSV\GVQ_API\Statistics\Repositories\CompanyPlayedQuizzesRepository;

class CompanyPlayedQuizzesProjector implements EventListener
{
    private $companyPlayedQuizzesRepository;

    public function __construct(CompanyPlayedQuizzesRepository $companyPlayedQuizzesRepository)
    {
        $this->companyPlayedQuizzesRepository = $companyPlayedQuizzesRepository;
    }

    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof QuizStarted) {
            $company = $payload->getQuiz()->getCompany();

            if ($company) {
                $language = $payload->getQuiz()->getLanguage();
                $this->companyPlayedQuizzesRepository->incrementCount(
                    $company->getId(),
                    $language
                );
            }
        }
    }
}
