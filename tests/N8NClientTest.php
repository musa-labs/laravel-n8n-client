<?php

namespace JPCaparas\N8N\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JPCaparas\N8N\N8NClient;
use Orchestra\Testbench\TestCase;

class N8NClientTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'JPCaparas\N8N\N8NServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default configuration
        $app['config']->set('n8n.base_uri', 'http://localhost:5678');
        $app['config']->set('n8n.api_token', 'test-token');
    }

    public function test_it_can_get_workflows(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'data' => [
                    ['id' => '1', 'name' => 'Test Workflow 1'],
                    ['id' => '2', 'name' => 'Test Workflow 2'],
                ],
            ])),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);

        $n8n = new N8NClient;
        $this->setProtectedProperty($n8n, 'client', $client);

        // Act
        $response = $n8n->getWorkflows();

        // Assert
        $this->assertCount(2, $response['data']);
        $this->assertEquals('1', $response['data'][0]['id']);

        // Verify request
        $this->assertCount(1, $container);

        $transaction = $container[0];
        $this->assertEquals('GET', $transaction['request']->getMethod());
        $this->assertEquals('/api/v1/workflows', $transaction['request']->getUri()->getPath());
    }

    public function test_it_can_create_workflow(): void
    {
        // Arrange
        $workflowData = [
            'name' => 'New Workflow',
            'nodes' => [],
            'connections' => [],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'id' => '123',
                'name' => 'New Workflow',
            ])),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);

        $n8n = new N8NClient;
        $this->setProtectedProperty($n8n, 'client', $client);

        // Act
        $response = $n8n->createWorkflow($workflowData);

        // Assert
        $this->assertEquals('123', $response['id']);
        $this->assertEquals('New Workflow', $response['name']);

        // Verify request
        $this->assertCount(1, $container);
        $transaction = $container[0];
        $this->assertEquals('POST', $transaction['request']->getMethod());
        $this->assertEquals('/api/v1/workflows', $transaction['request']->getUri()->getPath());
        $this->assertEquals('application/json', $transaction['request']->getHeader('Content-Type')[0]);
    }

    public function test_it_handles_api_errors(): void
    {
        // Arrange
        $mock = new MockHandler([
            new Response(404, [], json_encode([
                'error' => 'Workflow not found',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $n8n = new N8NClient;
        $this->setProtectedProperty($n8n, 'client', $client);

        // Act
        $response = $n8n->getWorkflow('non-existent-id');

        // Assert
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Workflow not found', $response['error']);
    }

    protected function setProtectedProperty($object, $property, $value): void
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
