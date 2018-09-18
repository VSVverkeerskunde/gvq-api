<?php declare(strict_types=1);

namespace VSV\GVQ_API\Statistics\Models;

use VSV\GVQ_API\Common\ValueObjects\Language;
use VSV\GVQ_API\Quiz\ValueObjects\QuizChannel;
use VSV\GVQ_API\Statistics\ValueObjects\NaturalNumber;
use VSV\GVQ_API\User\ValueObjects\Email;

class DetailedTopScore extends TopScore
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @var QuizChannel
     */
    private $channel;

    /**
     * @param Email $email
     * @param Language $language
     * @param QuizChannel $channel
     * @param NaturalNumber $score
     */
    public function __construct(
        Email $email,
        Language $language,
        QuizChannel $channel,
        NaturalNumber $score
    ) {
        parent::__construct($email, $score);

        $this->language = $language;
        $this->channel = $channel;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return QuizChannel
     */
    public function getChannel(): QuizChannel
    {
        return $this->channel;
    }
}
