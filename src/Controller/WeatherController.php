<?php

namespace App\Controller;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WeatherController extends AbstractController
{
    #[Route('/weather/{country}/{city}', name: 'app_weather_city')]
    #[IsGranted('ROLE_WEATHER_CITY')]
    public function city(
        string                $city,
        string               $country,
        WeatherUtil $util,
    ): Response
    {

        $res = $util->getWeatherForCountryAndCity($city, $country);

        if(!$res) {
            throw $this->createNotFoundException("Measurements not found");
        }

        $location = $res['location'];
        $measurements = $res['measurements'];

        if(!$location || !$measurements) {
            throw $this->createNotFoundException("Measurements not found");
        }

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
