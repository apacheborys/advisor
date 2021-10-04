<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdvisorControllerTest extends WebTestCase
{
    /**
     * @dataProvider createAdvisorProvider
     * @covers \App\Controller\AdvisorController::create
     */
    public function testCreateAdvisor(string $payload, int $expectedStatus, string $expectedResponse): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/v1/advisor', [], [], [], $payload);
        $response = $client->getResponse();

        self::assertSame($expectedStatus, $response->getStatusCode());

        $resultAdvisor = json_decode($response->getContent(), true);
        unset($resultAdvisor['id']);
        self::assertSame(json_decode($expectedResponse, true), $resultAdvisor);
    }

    public function createAdvisorProvider(): iterable
    {
        yield 'success case' => [
            'payload' => '{
                "name": "advisor nr 3",
                "description": "some description",
                "availability": true,
                "pricePerMinute": {
                    "amount": "300",
                    "currency": "EUR"
                },
                "languages": [
                    {
                        "locale": "de_DE"
                    }
                ]
            }',
            'expectedStatus' => 201,
            'expectedResponse' => '{
               "name":"advisor nr 3",
               "description":"some description",
               "availability":true,
               "pricePerMinute":{
                  "amount":"300",
                  "currency":"EUR"
               },
               "languages":[
                  [
                     "de_DE"
                  ]
               ]
            }',
        ];
    }
}
