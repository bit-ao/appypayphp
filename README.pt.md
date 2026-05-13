# AppyPay PHP SDK

SDK em PHP para o gateway de pagamentos [AppyPay](https://appypay.co.ao) (Angola).

Ler noutra língua: [English](README.md)

## Requisitos

- PHP 8.1+
- Composer

## Instalação

```bash
composer require bit/appypayphp
```

## Configuração

Defina as variáveis de ambiente (ou passe-as via `AppyPayConfig`):

```bash
APPYPAY_CLIENT_ID=...
APPYPAY_CLIENT_SECRET=...
APPYPAY_RESOURCE=...
APPYPAY_AUTH_URL=https://login.microsoftonline.com/.../oauth2/token
APPYPAY_API_URL=https://apiservices.appypay.co.ao
APPYPAY_VERSION=v2.0

# Terminal / POS
APPYPAY_POS_CODE=...

# IDs dos métodos de pagamento (fornecidos pela AppyPay ao comerciante)
APPYPAY_GPO=...   # Multicaixa Express / QR
APPYPAY_REF=...   # Referência bancária
APPYPAY_ETPA=...  # Terminal físico
```

## Início rápido

```php
use Bit\AppyPay\AppyPay;
use Bit\AppyPay\Dtos\ExpressChargeDto;
use Bit\AppyPay\Dtos\PaymentInfoGpoDto;
use Bit\AppyPay\Enums\PaymentMethod;

$appy = AppyPay::fromEnv();

$charge = new ExpressChargeDto();
$charge->amount = 1500;
$charge->description = 'Encomenda #123';
$charge->merchantTransactionId = 'TX-000123';
$charge->paymentMethod = PaymentMethod::express;
$charge->paymentInfo = new PaymentInfoGpoDto('923000000');

$resposta = $appy->charge($charge);
```

## Métodos de pagamento

| Método     | Endpoint        | Notas                                                  |
|------------|-----------------|--------------------------------------------------------|
| `express`  | `POST /charges` | Multicaixa Express (push para o telemóvel)             |
| `aexpress` | `POST /charges` | Igual ao `express`, assíncrono (`vnd.appypay.asyncapi`)|
| `ref`      | `POST /charges` | Referência Multicaixa                                  |
| `qr`       | `POST /qr-codes`| QR code estático / dinâmico (posCode vem do `.env`)    |

## Outras operações

```php
// Consultar uma cobrança
$appy->getCharge($id);                        // por UUID do gateway

// Listar cobranças (página por omissão = 50)
$appy->listCharges($queryDto);

// Referências permanentes
$appy->registerReference($registerDto);
$appy->listReferences($queryDto);
```

## Cache do token

Por omissão o token é guardado em disco (`sys_get_temp_dir()/appypay_oauth_token.json`). Pode fornecer o seu próprio storage:

```php
use Bit\AppyPay\Storage\RedisTokenStorage;
use Bit\AppyPay\Storage\MemoryTokenStorage;

$appy = AppyPay::fromEnv(new RedisTokenStorage($redis));
```

## Tratamento de erros

Chamadas HTTP falhadas lançam `Bit\AppyPay\Exception\AppyPayException`. Use `AppyPayErrorHandler::handle()` para normalizar:

```php
use Bit\AppyPay\Exception\AppyPayErrorHandler;
use Bit\AppyPay\Exception\AppyPayException;

try {
    $appy->charge($charge);
} catch (AppyPayException $e) {
    $erro = AppyPayErrorHandler::handle($e); // ['success'=>false, 'code'=>..., 'message'=>..., 'original'=>...]
}
```

## Licença

MIT
