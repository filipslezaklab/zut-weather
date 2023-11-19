<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        WeatherUtil                  $utils,
        #[MapQueryParameter] string  $city,
        #[MapQueryParameter] ?string $country,
        #[MapQueryParameter] ?string $format = 'json',
        #[MapQueryParameter] ?bool $twig = false,
    ): JsonResponse|Response
    {
        if($format && ($format != 'json' && $format != 'csv')) {
            throw new BadRequestHttpException('Format can only be json or csv');
        }
        $res = $utils->getWeatherForCountryAndCity($city, $country);
        $measurements = $res['measurements'];
        $csvString = "City,Country,Date,Celsius,Fahrenheit\n";
        if(!$twig) {
            if(!$measurements) {
                if($format == 'json') {
                    return $this->json([]);
                } else {
                    $response = new Response($csvString);
                    $response->headers->set('Content-Type', 'text/csv');
                    return $response;
                }
            }
            if($format == 'json') {
                return $this->json([
                    'measurements' => array_map(fn(Measurement $m) => [
                        'city' => $city,
                        'country' => $country,
                        'date' => $m->getDate()->format('y-m-d'),
                        'celsius' => (string)$m->getTemperature(),
                        'fahrenheit' => $m->getFahrenheit(),
                    ], $measurements),
                ]);
            }
            foreach ($measurements as $measurement) {
                $csvString = $csvString . $city . "," . $country . ',' . $measurement->getDate()->format('y-m-d') . ',' . $measurement->getTemperature() . ',' . $measurement->getFahrenheit() . "\r\n";
            }
            $response = new Response($csvString);
            $response->headers->set('Content-Type', 'text/csv');
            return $response;
        } else {
            if($format == 'json') {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            } else {
                return $this->render('weather_api/index.csv.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            }
        }
    }
}
