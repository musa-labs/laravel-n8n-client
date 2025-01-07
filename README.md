# jpcaparas/laravel-n8n-client

![Tests](https://github.com/jpcaparas/laravel-n8n-client/actions/workflows/tests.yml/badge.svg)

A Laravel library that interfaces with the [self-hosted N8N.io](https://docs.n8n.io/hosting/community-edition-features/) [REST API](https://docs.n8n.io/api/api-reference/).

<img width="1286" alt="image" src="https://github.com/user-attachments/assets/d21e7b2c-f30b-4404-859a-4c926fb3a97b" />

## Installation

You can install the package via Composer:

```bash
composer require jpcaparas/laravel-n8n-client
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="JPCaparas\N8N\N8NServiceProvider" --tag="config"
```

Set these credentials inside your `.env` file:

```
N8N_BASE_URI=http://[your_n8n_instance]:[your_n8n_port]
N8N_API_TOKEN=[your_n8n_api_token]
```

## Local testing

If you want to interact with the package locally, you can use the Laravel skeleton provided by `orchestra/testbench` and its associated Tinker REPL:

1. Publish the configuration file to the Laravel skeleton.

```bash
composer test:publish
```

1. Modify `.env` API credentials on the skeleton as needed:

```bash
N8N_BASE_URI=http://localhost:5678
N8N_API_TOKEN=[your_n8n_api_token]
```

2. Enter the Tinker REPL:

```bash
composer test:tinker
```

2. Create an instance of the N8N client:

```php
$n8n = app(\JPCaparas\N8N\N8NClient::class);
```

3. Make API requests using the N8N client:

```php
$response = $n8n->get('valid-endpoint');
$response = $n8n->post('valid-endpoint', ['data' => 'value']);
```

There are also convenience methods for the most common API endpoints:

```php
$response = $n8n->getWorkflows();
$response = $n8n->getWorkflow('workflow-id');
$response = $n8n->createWorkflow(['name' => 'New Workflow']);
$response = $n8n->updateWorkflow('workflow-id', ['name' => 'Updated Workflow']);
```

## Tests

```bash
composer test
```

## License

MIT
