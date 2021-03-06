<?php declare(strict_types=1);

namespace App\Twitter\MessageFactory;

use App\Pollution\Box\Box;
use App\Pollution\PollutionLevel\PollutionLevel;
use Symfony\Component\Translation\TranslatorInterface;

class EmojiMessageFactory extends AbstractMessageFactory
{
    /** @var TranslatorInterface $translator */
    protected $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function compose(): MessageFactoryInterface
    {
        $this->message .= sprintf("%s\n", $this->title);

        /** @var array $pollutant */
        foreach ($this->pollutantList as $pollutant) {
            /** @var Box $box */
            foreach ($pollutant as $box) {
                $this->message .= sprintf("%s %s: %.0f %s \n", $this->getEmoji($box), $box->getPollutant()->getName(), $box->getData()->getValue(), $box->getPollutant()->getUnitPlain());
            }
        }

        $this->message .= sprintf("%s", $this->link);

        return $this;
    }

    protected function getEmoji(Box $box): string
    {
        $translationKey = sprintf('air_quality.index.%d.icon', $box->getPollutionLevel());

        return $this->translator->trans($translationKey);
    }
}
