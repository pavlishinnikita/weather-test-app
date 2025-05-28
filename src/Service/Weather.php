<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Weather
{
    private string $baseUrl = "https://api.weatherapi.com/v1/current.json";
    public function __construct(
        private HttpClientInterface $httpClient,
        private ParameterBagInterface $parameterBag,
        private LoggerInterface $logger
    )
    {

    }

    private function logResponse(array $response)
    {
        $filesystem = new Filesystem();
        $filePath = $this->parameterBag->get('kernel.project_dir') . '/var/log/weather_log.txt';
        $emptyMark = "<EMPTY>";
        try {
            $logRow = sprintf("%s - Погода в %s: %s°C, %s" . PHP_EOL,
                date('Y-m-d H:i:s'),
                $response['city'] ?? $emptyMark,
                $response['temperature'] ?? $emptyMark,
                $response['condition'] ?? $emptyMark,
            );
            $filesystem->dumpFile($filePath, $logRow);
        } catch (\Throwable $e) {
            $this->logger->error('weather_log_error', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param string $city
     * @return array
     */
    public function getWeatherDataByCity(string $city) : array
    {
        try {
            $resp = $this->httpClient->request(
                "GET",
                $this->baseUrl,
                [
                    'query' => ["key" => $this->parameterBag->get("weather_api_key"), 'q' => $city],
                ]
            );
            $preparedResponse = $this->prepareResponse(json_decode($resp->getContent(false), true));
            if(isset($preparedResponse['error'])) {
                return [
                    'error' => $preparedResponse['error'],
                ];
            }
            $this->logResponse($preparedResponse);
        } catch (\Throwable $e) {
            $this->logger->error("weather_request_error", [
                'message' => $e->getMessage(),
            ]);
            return [
                "error" => $e->getMessage(),
            ];
        }
        return $preparedResponse;
    }

    /**
     * Prepares response with specific format
     * @param mixed $rawResponse
     * @return array
     */
    private function prepareResponse(mixed $rawResponse) : array
    {
        $emptyMark = "<EMPTY>";
        $preparedResponse = [
            'city' => $rawResponse['location']['name'] ?? $emptyMark,
            'country' => $rawResponse['location']['country'] ?? $emptyMark,
            'temperature' => $rawResponse['current']['temp_c'] ?? $emptyMark,
            'condition' => $rawResponse['current']['condition']['text'] ?? $emptyMark,
            'condition_icon' => $rawResponse['current']['condition']['icon'] ?? $emptyMark,
            'humidity' => $rawResponse['current']['humidity'] ?? $emptyMark,
            'wind_speed' => $rawResponse['current']['wind_kph'] ?? $emptyMark,
            'last_updated' => $rawResponse['current']['last_updated'] ?? $emptyMark,
        ];
        if (isset($rawResponse['error'])) {
            $preparedResponse['error'] = $rawResponse['error']['message'] ?? 'Internal error';
        }
        return $preparedResponse;
    }
}