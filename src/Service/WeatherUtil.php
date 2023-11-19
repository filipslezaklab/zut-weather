<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Measurement;
use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function PHPUnit\Framework\throwException;

class WeatherUtil
{
    private $locationRepo;
    private $measurementRepo;

    public function __construct(LocationRepository $location, MeasurementRepository $measurement) {
        $this->locationRepo = $location;
        $this->measurementRepo = $measurement;
    }
    /**
    * @param Location $location
    * @return Measurement[]
     */
    public function getWeatherForLocation(Location $location): array
    {
        return $this->measurementRepo->findByLocation($location);
    }

    /**
     * @param string $city
     * @param string|null $countryCode
     * @return array|null
     */
    public function getWeatherForCountryAndCity( string $city, ?string $countryCode): ?array
    {
        $location = $this->locationRepo->findOneByCity($city, $countryCode);
        if(!$location) {
            return null;
        }
        $measurements = $this->measurementRepo->findByLocation($location);
        return ['measurements' => $measurements, 'location' => $location];
    }
}