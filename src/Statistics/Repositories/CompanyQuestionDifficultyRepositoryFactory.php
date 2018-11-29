<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories;

use Ramsey\Uuid\UuidInterface;
use VSV\GVQ_API\Common\ValueObjects\NotEmptyString;
use VSV\GVQ_API\Question\Repositories\QuestionRepository;

class CompanyQuestionDifficultyRepositoryFactory extends AbstractRedisRepository
{
    /**
     * @var CompanyQuestionCounterRepositoryFactory
     */
    private $answeredCorrectRepositoryFactory;

    /**
     * @var CompanyQuestionCounterRepositoryFactory
     */
    private $answeredInCorrectRepositoryFactory;

    /**
     * @var QuestionRepository
     */
    private $questionRepository;

    /**
     * @param \Redis $redis
     * @param CompanyQuestionCounterRepositoryFactory $answeredCorrectRepositoryFactory
     * @param CompanyQuestionCounterRepositoryFactory $answeredInCorrectRepositoryFactory
     * @param QuestionRepository $questionRepository
     */
    public function __construct(
        \Redis $redis,
        CompanyQuestionCounterRepositoryFactory $answeredCorrectRepositoryFactory,
        CompanyQuestionCounterRepositoryFactory $answeredInCorrectRepositoryFactory,
        QuestionRepository $questionRepository
    ) {
        parent::__construct($redis);

        $this->answeredCorrectRepositoryFactory = $answeredCorrectRepositoryFactory;
        $this->answeredInCorrectRepositoryFactory = $answeredInCorrectRepositoryFactory;
        $this->questionRepository = $questionRepository;
    }

    public function forCompany(UuidInterface $companyId): QuestionDifficultyRedisRepository
    {
        return new QuestionDifficultyRedisRepository(
            $this->redis,
            $this->answeredCorrectRepositoryFactory->forCompany($companyId),
            $this->answeredInCorrectRepositoryFactory->forCompany($companyId),
            $this->questionRepository,
            new NotEmptyString('difficulty_company_' . $companyId->toString())
        );
    }
}
