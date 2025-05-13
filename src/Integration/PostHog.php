<?php

namespace App\Integration;

use Symfony\Component\HttpClient\HttpClient;

class PostHog
{
    public function __construct(
        private string $apiKey,
    )
    {
    }

    public function getViewsCount(string $path): int
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'https://eu.posthog.com/api/projects/39595/query/', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf('Bearer %s', trim($this->apiKey))
            ],
            'body' => json_encode([
                'query' => [
                    'kind' => 'HogQLQuery',
                    'query' => sprintf('select count(*) from events where event=\'$pageview\'  AND properties.$pathname = \'%s\'', $path)
                ]
            ])
        ]);

        $data = json_decode($response->getContent(), true);

        return (int)$data['results'][0][0];
    }
}
