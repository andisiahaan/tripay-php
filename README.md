# tripay-php

Unofficial PHP SDK for the Tripay.co.id API.

This package provides a small, focused wrapper around Tripay endpoints using Guzzle and PSR-4 autoloading. It's suitable for small projects and internal tools. Use at your own discretion — this is an unofficial client.

Package namespace: `AndiSiahaan\\Tripay`

## Installation

Install via Composer:

```bash
composer require andisiahaan/tripay-php
```

Or include as a repository and require it in your `composer.json` during development.

## Quick usage

```php
require 'vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$client = new Client(getenv('TRIPAY_API_KEY'), getenv('TRIPAY_PRIVATE_KEY'), false, getenv('TRIPAY_MERCHANT_CODE'));

// Example: list payment channels
$channels = $client->paymentChannels()->list();
print_r($channels);
```

For transaction creation and Open Payment, consult the examples in `examples/` for full payloads.

## Running tests

```powershell
php vendor\\bin\\phpunit -v
```

## Debugging

The client collects last request/response debug information via Guzzle `on_stats`. Use `$client->getDebugs()` after a request to inspect the raw HTTP request and response for troubleshooting.

## Contributing

Contributions are welcome. Please open issues or pull requests on GitHub. Keep changes small and include tests where appropriate.

## License

MIT — see `LICENSE`.

## Links

- GitHub: https://github.com/andisiahaan/tripay-php
- Packagist: (will be available after you publish)

## Examples

See the `examples/` folder for runnable examples. Set these environment variables before running (PowerShell):

```powershell
$env:TRIPAY_API_KEY='your-api-key'; $env:TRIPAY_PRIVATE_KEY='your-private-key'; $env:TRIPAY_MERCHANT_CODE='YOUR_MERCHANT_CODE'; php examples/example_create_transaction.php
```
