<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country?}', name: 'app_weather_city')]
    #[IsGranted('ROLE_WEATHER_CITY')]
    public function city(
        string                $city,
        ?string               $country,
        MeasurementRepository $measurementRepository,
        LocationRepository    $locationRepository,
    ): Response
    {
        $location = $locationRepository->findOneByCity($city, $country);
        if (!$location) {
            throw $this->createNotFoundException("Location not found");
        }
        $measurements = $measurementRepository->findByLocation($location);
        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
