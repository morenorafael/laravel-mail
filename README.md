# Laravel Mail

Este paquete extiende el funcionamiento del servicio Mail de Laravel

## Instalación

```bash
composer require morenorafael/laravel-mail
```

## Configuración

Agregamos al archivo de configuración `config/services.php` lo siguiente:

```php
...

'sendgrid' => [
    'url' => env('SENDGRID_URL'),
    'key' => env('SENDGRID_KEY'),
],

...
```


Agregamos al archivo de configuración `config/mail.php`, dentro del array `mailers` lo siguiente:

```php
...

'sendgrid' => [
    'transport' => 'sendgrid',
],

...
```

## Uso

```php
$user = new \App\Models\User([
    'email' => 'name@mail.com',
    'name' => 'Your Name'
]);

\Illuminate\Support\Facades\Mail::send('mails.welcome', [
    'email' => $user->email,
    'name' => $user->name,
], function ($mail) use ($user) {
    $mail->to($user->email, $user->name)->subject('Test');
});
```

## Contribuir
Los pull request son bienvenidos. Para cambios importantes, abra un issue primero para discutir qué le gustaría cambiar.

Asegúrese de actualizar las pruebas según corresponda.

## Licencia
[MIT](https://github.com/morenorafael/laravel-mail/blob/master/LICENSE.md)