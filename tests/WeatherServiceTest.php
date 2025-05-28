<?php

namespace App\Tests;

use App\Service\Weather;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WeatherServiceTest extends TestCase
{
    public function testResponseEmptyError(): void
    {
        $city = "London"; // btw it's the capital of Great Britain

        $errorMessage = 'Internal error';

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getContent')
            ->with(false)
            ->willReturn(json_encode([
                'error' => $errorMessage,
            ]));

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringStartsWith('https://api.weatherapi.com/v1/current.json'),
                $this->callback(function (array $options) use ($city) {
                    return isset($options['query']['key']) && isset($options['query']['q']) && $options['query']['q'] === $city; // make sure that query is full and contains all parts
                })
            )
            ->willReturn($mockResponse);

        $mockParameterBag = $this->createMock(ParameterBagInterface::class);
        $mockParameterBag->method('get')
            ->with('weather_api_key')
            ->willReturn('TEST_API_KEY');

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->never())->method('info');

        $weatherService = new Weather($mockHttpClient, $mockParameterBag, $mockLogger);

        $result = $weatherService->getWeatherDataByCity($city);
        $this->assertArrayHasKey("error", $result, "Response has no errors");

        $this->assertEquals($errorMessage, $result['error']);
    }
}
