<?php

namespace App\Controller;

use App\Service\Weather;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function showWeatherHomePage() : Response
    {
        return new Response("I'm homepage, implement me please");
    }

    #[Route('/api/weather/{city}', name: 'weather_request', requirements: ['city' => "[а-яА-Яa-zA-Z- \,\/]+"])]
    public function showWeatherPage(string $city, Weather $weather) : Response
    {
        $resp = $weather->getWeatherDataByCity($city);
        return $this->render('info.html.twig', [
            'error' => $resp['error'] ?? '',
            'weatherInfo' => $resp,
        ]);
    }
}