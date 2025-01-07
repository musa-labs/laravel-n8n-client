# jpcaparas/n8n-client

A Laravel library that interfaces with the self-hosted N8N.io REST API.

## Installation

You can install the package via composer:

```bash
composer require jpcaparas/n8n-client
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="JPCaparas\N8N\N8NServiceProvider" --tag="config"
```

Add your N8N API base URL and token to your `.env` file:

```
N8N_BASE_URI=http://localhost:5678
N8N_API_TOKEN=your_api_token
```

## Testing Locally Using Tinker

To test the package locally using Tinker, follow these steps:

1. Publish the configuration file to the Laravel skeleton provided by `orchestra/testbench`:

```bash
./vendor/bin/testbench vendor:publish --provider="JPCaparas\N8N\N8NServiceProvider" --tag="config"
```

1. Modify the skeleton `.env` as needed:

```bash
N8N_BASE_URI=http://localhost:5678
N8N_API_TOKEN=[your_api_token]
```

2. Open Tinker:

```bash
php artisan tinker
```

2. Create an instance of the N8N client:

```php
$n8n = app(JPCaparas\N8N\N8NClient::class);
```

3. Make API requests using the N8N client:

```php
$response = $n8n->get('your-endpoint');
$response = $n8n->post('your-endpoint', ['data' => 'value']);
```

## Running Automated Tests

To run the automated tests, use the following command:

```bash
composer test
```
