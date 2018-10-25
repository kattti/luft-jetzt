<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Station;
use App\Entity\TwitterSchedule;
use App\Entity\Zip;
use App\Geocoding\Query\GeoQueryInterface;
use App\Pollution\PollutionDataFactory\PollutionDataFactory;
use App\Pollution\StationFinder\StationFinderInterface;
use App\SeoPage\SeoPage;
use Caldera\GeoBasic\Coord\Coord;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController extends Controller
{
    protected function getCoordByRequest(Request $request, GeoQueryInterface $geoQuery): ?Coord
    {
        $latitude = (float) $request->query->get('latitude');
        $longitude = (float) $request->query->get('longitude');
        $query = $request->query->get('query');
        $zipCode = $request->query->get('zip');

        if (($query && preg_match('/^([0-9]{5,5})$/', $query)) || $zipCode) {
            $zip = $this->getDoctrine()->getRepository(Zip::class)->findOneByZip($zipCode ?? $query);

            return $zip;
        }

        if ($latitude && $longitude) {
            $coord = new Coord(
                $latitude,
                $longitude
            );

            return $coord;
        }

        if ($query) {
            $result = $geoQuery->query($query);

            $firstResult = array_pop($result);

            if ($firstResult) {
                $coord = new Coord($firstResult['value']['latitude'], $firstResult['value']['longitude']);

                return $coord;
            }
        }

        return null;
    }

    protected function getStationListForCity(City $city): array
    {
        return $this->getDoctrine()->getRepository(Station::class)->findActiveStationsForCity($city);
    }

    protected function createBoxListForStationList(PollutionDataFactory $pollutionDataFactory, array $stationList): array
    {
        $stationsBoxList = [];

        /** @var Station $station */
        foreach ($stationList as $station) {
            $stationsBoxList[$station->getStationCode()] = $pollutionDataFactory
                ->setStation($station)
                ->createDecoratedBoxList();
        }

        return $stationsBoxList;
    }

    protected function getCityBySlug(string $citySlug): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneBySlug($citySlug);
    }

    protected function getTwitterScheduleByRequest(Request $request): ?TwitterSchedule
    {
        $scheduleId = $request->query->getInt('scheduleId');
        $schedule = $this->getDoctrine()->getRepository(TwitterSchedule::class)->find($scheduleId);

        return $schedule;
    }

    protected function findCityForName(string $cityName): ?City
    {
        return $this->getDoctrine()->getRepository(City::class)->findOneByName($cityName);
    }
}
