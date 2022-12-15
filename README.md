## Streply SDK for Symfony framework

### Install

Install the `streply/streply-bundle` package:

```bash
composer require streply/streply-symfony
```

### Enable bundle
Add the bundle to the list of registered bundles in `config/bundles.php`
```php
return [
    Streply\StreplyBundle\StreplyBundle::class => ['all' => true]
];
```

### Config
Add default configuration in `config/packages/streply.yaml`
```bash
streply:
  dsn: '%env(STREPLY_DSN)%'
```

### Environment variable
Add DSN info to `.env` file
```bash
###> streply/streply-bundle ###
STREPLY_DSN="https://clientPublicKey@api.streply.com/projectId"
###< streply/streply-bundle ###
```
