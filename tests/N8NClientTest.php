<?php

namespace JPCaparas\N8N\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JPCaparas\N8N\N8NClient;
use Orchestra\Testbench\TestCase;

class N8NClientTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['JPCaparas\N8N\N8NServiceProvider'];
    }

    public function test_get_request()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['success' => true])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $n8nClient = new N8NClient([
            'base_uri' => 'http://localhost:5678',
            'api_token' => 'test_token',
        ]);

        $response = $n8nClient->get('test-endpoint');

        $this->assertEquals(['success' => true], $response);
    }

    public function test_post_request()
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['success' => true])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $n8nClient = new N8NClient([
            'base_uri' => 'http://localhost:5678',
            'api_token' => 'test_token',
        ]);

        $response = $n8nClient->post('test-endpoint', ['data' => 'test']);

        $this->assertEquals(['success' => true], $response);
    }

    public function test_handle_exception()
    {
        $mock = new MockHandler([
            new RequestException('Error Communicating with Server', new Request('GET', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $n8nClient = new N8NClient([
            'base_uri' => 'http://localhost:5678',
            'api_token' => 'test_token',
        ]);

        $response = $n8nClient->get('test-endpoint');

        $this->assertArrayHasKey('error', $response);
    }
}
