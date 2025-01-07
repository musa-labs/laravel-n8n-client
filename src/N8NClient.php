<?php

namespace JPCaparas\N8N;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class N8NClient
{
    protected $client;

    protected const URI_PREFIX = '/api/v1/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => Config::get('n8n.base_uri'),
            'headers' => [
                'X-N8N-API-KEY' => Config::get('n8n.api_token'),
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint, $params = []): array
    {
        try {
            $response = $this->client->request('GET', self::URI_PREFIX.$endpoint, [
                'query' => $params,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function post(string $endpoint, $data = []): array
    {
        try {
            $response = $this->client->request('POST', self::URI_PREFIX.$endpoint, [
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function getWorkflows(array $params = []): array
    {
        return $this->get('workflows', $params);
    }

    public function getWorkflow(string $id)
    {
        return $this->get("workflows/{$id}");
    }

    public function createWorkflow(array $data): array
    {
        return $this->post('workflows', $data);
    }

    public function updateWorkflow(string $id, array $data): array
    {
        return $this->request('PUT', "workflows/{$id}", [
            'json' => $data,
        ]);
    }

    public function deleteWorkflow(string $id): array
    {
        return $this->request('DELETE', "workflows/{$id}");
    }

    public function activateWorkflow(string $id): array
    {
        return $this->post("workflows/{$id}/activate");
    }

    public function deactivateWorkflow(string $id): array
    {
        return $this->post("workflows/{$id}/deactivate");
    }

    public function getExecutions(array $params = []): array
    {
        return $this->get('executions', $params);
    }

    public function getExecution(string $id, bool $includeData = false): array
    {
        return $this->get("executions/{$id}", [
            'includeData' => $includeData,
        ]);
    }

    public function getTags(array $params = []): array
    {
        return $this->get('tags', $params);
    }

    public function createTag(array $data): array
    {
        return $this->post('tags', $data);
    }

    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->client->request($method, self::URI_PREFIX.$endpoint, $options);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(RequestException $e): array
    {
        if ($e->hasResponse()) {
            return json_decode($e->getResponse()->getBody(), true);
        }

        return ['error' => $e->getMessage()];
    }
}
