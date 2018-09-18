<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\Models\DetailedTopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

/**
 * @ORM\Entity()
 * @ORM\Table(name="detailed_top_score")
 */
class DetailedTopScoreEntity
{
    /**
     * @var string
     * @ORm\Id()
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     * @ORm\Id()
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    private $language;

    /**
     * @var string
     * @ORm\Id()
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $channel;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $score;

    /**
     * @param string $email
     * @param string $language
     * @param string $channel
     * @param int $score
     */
    private function __construct(
        string $email,
        string $language,
        string $channel,
        int $score
    ) {
        $this->email = $email;
        $this->language = $language;
        $this->channel = $channel;
        $this->score = $score;
    }

    /**
     * @param DetailedTopScore $detailedTopScore
     * @return DetailedTopScoreEntity
     */
    public static function fromDetailedTopScore(
        DetailedTopScore $detailedTopScore
    ): DetailedTopScoreEntity {
        return new DetailedTopScoreEntity(
            $detailedTopScore->getEmail()->toNative(),
            $detailedTopScore->getLanguage()->toNative(),
            $detailedTopScore->getChannel()->toNative(),
            $detailedTopScore->getScore()->toNative()
        );
    }

    /**
     * @return DetailedTopScore
     */
    public function toDetailedTopScore(): DetailedTopScore
    {
        return new DetailedTopScore(
            new Email($this->email),
            new Language($this->language),
            new QuizChannel($this->channel),
            new NaturalNumber($this->score)
        );
    }
}
