# AppyPay PHP SDK

PHP SDK for the [AppyPay](https://appypay.co.ao) payment gateway (Angola).

Read this in: [Português](README.pt.md)

## Requirements

- PHP 8.1+
- Composer

## Install

```bash
composer require bit/appypayphp
```

## Configuration

Set the following environment variables (or pass them through `AppyPayConfig`):

```bash
APPYPAY_CLIENT_ID=...
APPYPAY_CLIENT_SECRET=...
APPYPAY_RESOURCE=...
APPYPAY_AUTH_URL=https://login.microsoftonline.com/.../oauth2/token
APPYPAY_API_URL=https://apiservices.appypay.co.ao
APPYPAY_VERSION=v2.0

# Terminal / POS
APPYPAY_POS_CODE=...

# Merchant-side payment method IDs (provided by AppyPay)
APPYPAY_GPO=...   # Multicaixa Express / QR
APPYPAY_REF=...   # Bank reference
APPYPAY_ETPA=...  # Physical terminal
```

## Quick start

```php
use Bit\AppyPay\AppyPay;
use Bit\AppyPay\Dtos\ExpressChargeDto;
use Bit\AppyPay\Dtos\PaymentInfoGpoDto;
use Bit\AppyPay\Enums\PaymentMethod;

$appy = AppyPay::fromEnv();

$charge = new ExpressChargeDto();
$charge->amount = 1500;
$charge->description = 'Order #123';
$charge->merchantTransactionId = 'TX-000123';
$charge->paymentMethod = PaymentMethod::express;
$charge->paymentInfo = new PaymentInfoGpoDto('923000000');

$response = $appy->charge($charge);
```

## Payment methods

| Method     | Endpoint        | Notes                                              |
|------------|-----------------|----------------------------------------------------|
| `express`  | `POST /charges` | Multicaixa Express (push to phone)                 |
| `aexpress` | `POST /charges` | Same as `express`, async (`vnd.appypay.asyncapi`)  |
| `ref`      | `POST /charges` | Bank reference (Multicaixa reference)              |
| `qr`       | `POST /qr-codes`| Static / dynamic QR code (posCode comes from env)  |

## Other operations

```php
// Single charge
$appy->getCharge($id);                        // by gateway UUID

// List charges (default page = 50)
$appy->listCharges($queryDto);

// Permanent references
$appy->registerReference($registerDto);
$appy->listReferences($queryDto);
```

## Token caching

By default tokens are cached on disk (`sys_get_temp_dir()/appypay_oauth_token.json`). You can supply your own storage:

```php
use Bit\AppyPay\Storage\RedisTokenStorage;
use Bit\AppyPay\Storage\MemoryTokenStorage;

$appy = AppyPay::fromEnv(new RedisTokenStorage($redis));
```

## Error handling

Failed HTTP calls throw `Bit\AppyPay\Exception\AppyPayException`. Use `AppyPayErrorHandler::handle()` to normalize them:

```php
use Bit\AppyPay\Exception\AppyPayErrorHandler;
use Bit\AppyPay\Exception\AppyPayException;

try {
    $appy->charge($charge);
} catch (AppyPayException $e) {
    $error = AppyPayErrorHandler::handle($e); // ['success'=>false, 'code'=>..., 'message'=>..., 'original'=>...]
}
```

## License

MIT
