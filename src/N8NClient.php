<?php

namespace JPCaparas\N8N;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class N8NClient
{
    protected $client;

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

    public function get($endpoint, $params = [])
    {
        try {
            $response = $this->client->request('GET', '/api/v1/' . $endpoint, [
                'query' => $params,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function post($endpoint, $data = [])
    {
        try {
            $response = $this->client->request('POST', '/api/v1/ ' . $endpoint, [
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function getWorkflows($params = [])
    {
        return $this->get('workflows', $params);
    }

    public function getWorkflow($id)
    {
        return $this->get("workflows/{$id}");
    }

    public function createWorkflow($data)
    {
        return $this->post('workflows', $data);
    }

    public function updateWorkflow($id, $data)
    {
        return $this->request('PUT', "workflows/{$id}", [
            'json' => $data,
        ]);
    }

    public function deleteWorkflow($id)
    {
        return $this->request('DELETE', "workflows/{$id}");
    }

    public function activateWorkflow($id)
    {
        return $this->post("workflows/{$id}/activate");
    }

    public function deactivateWorkflow($id)
    {
        return $this->post("workflows/{$id}/deactivate");
    }

    public function getExecutions($params = [])
    {
        return $this->get('executions', $params);
    }

    public function getExecution($id, $includeData = false)
    {
        return $this->get("executions/{$id}", [
            'includeData' => $includeData,
        ]);
    }

    public function getTags($params = [])
    {
        return $this->get('tags', $params);
    }

    public function createTag($data)
    {
        return $this->post('tags', $data);
    }

    private function request($method, $endpoint, $options = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(RequestException $e)
    {
        if ($e->hasResponse()) {
            return json_decode($e->getResponse()->getBody(), true);
        }

        return ['error' => $e->getMessage()];
    }
}
