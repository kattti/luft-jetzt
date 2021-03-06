<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DataRepository")
 * @ORM\Table(name="data")
 * @JMS\ExclusionPolicy("ALL")
 */
class Data
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Station $station
     * @ORM\ManyToOne(targetEntity="Station", inversedBy="datas")
     * @ORM\JoinColumn(name="station_id", referencedColumnName="id")
     */
    protected $station;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="float", nullable=false)
     * @JMS\Expose()
     */
    protected $value;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Expose()
     */
    protected $pollutant;

    public function getId(): int
    {
        return $this->id;
    }

    public function getStation(): Station
    {
        return $this->station;
    }

    public function setStation(Station $station): Data
    {
        $this->station = $station;

        return $this;
    }

    public function getStationId(): ?int
    {
        if ($this->station) {
            return $this->station->getId();
        }

        return null;
    }

    public function getDateTimeFormatted(): string
    {
        return $this->dateTime->format('Y-m-d H:i:s');
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): Data
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): Data
    {
        $this->value = $value;

        return $this;
    }

    public function getPollutant(): int
    {
        return $this->pollutant;
    }

    public function setPollutant(int $pollutant): Data
    {
        $this->pollutant = $pollutant;

        return $this;
    }

    public function getProvider(): string
    {
        return $this->station->getProvider();
    }

    public function isIndexable(): bool
    {
        $dateTime = new \DateTimeImmutable();
        $dateTime->sub(new \DateInterval('P1W'));

        return $dateTime >= $this->dateTime;
    }
}
