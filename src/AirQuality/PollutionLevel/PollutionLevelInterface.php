<?php declare(strict_types=1);

namespace App\AirQuality\PollutionLevel;

use App\Entity\Data;

interface PollutionLevelInterface
{
    public function getLevel(Data $data): int;
    public function getLevels(): array;
    public function getPollutionIdentifier(): string;
}
