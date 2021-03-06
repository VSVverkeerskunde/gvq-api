<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Repositories\Entities;

use Doctrine\ORM\Mapping as ORM;
use VSV\GVQ_API\Statistics\Models\TopScore;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

/**
 * @ORM\Entity()
 * @ORM\Table(name="top_score")
 */
class TopScoreEntity
{
    /**
     * @var string
     * @ORM\Id()
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $email;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $score;

    /**
     * @param string $email
     * @param int $score
     */
    private function __construct(string $email, int $score)
    {
        $this->email = $email;
        $this->score = $score;
    }

    /**
     * @param TopScore $topScore
     * @return TopScoreEntity
     */
    public static function fromTopScore(TopScore $topScore): TopScoreEntity
    {
        return new TopScoreEntity(
            $topScore->getEmail()->toNative(),
            $topScore->getScore()->toNative()
        );
    }

    /**
     * @return TopScore
     */
    public function toTopScore(): TopScore
    {
        return new TopScore(
            new Email($this->email),
            new NaturalNumber($this->score)
        );
    }
}
