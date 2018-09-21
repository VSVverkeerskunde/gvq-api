<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Projectors;

use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListener;
use VSV\GVQ_API\Quiz\Events\AnsweredCorrect;
use VSV\GVQ_API\Quiz\Events\AnsweredIncorrect;
use VSV\GVQ_API\Statistics\Repositories\CategoryDifficultyRepository;

class CategoryDifficultyProjector implements EventListener
{
    /**
     * @var CategoryDifficultyRepository
     */
    private $categoryCorrectRepository;

    /**
     * @var CategoryDifficultyRepository
     */
    private $categoryInCorrectRepository;

    /**
     * @param CategoryDifficultyRepository $categoryCorrectRepository
     * @param CategoryDifficultyRepository $categoryInCorrectRepository
     */
    public function __construct(
        CategoryDifficultyRepository $categoryCorrectRepository,
        CategoryDifficultyRepository $categoryInCorrectRepository
    ) {
        $this->categoryCorrectRepository = $categoryCorrectRepository;
        $this->categoryInCorrectRepository = $categoryInCorrectRepository;
    }

    /**
     * @inheritdoc
     */
    public function handle(DomainMessage $domainMessage)
    {
        $payload = $domainMessage->getPayload();

        if ($payload instanceof AnsweredCorrect) {
            $this->categoryCorrectRepository->increment(
                $payload->getQuestion()->getCategory(),
                $payload->getQuestion()->getLanguage()
            );
        } elseif ($payload instanceof AnsweredIncorrect) {
            $this->categoryInCorrectRepository->increment(
                $payload->getQuestion()->getCategory(),
                $payload->getQuestion()->getLanguage()
            );
        }
    }
}
