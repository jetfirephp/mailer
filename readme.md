## JetFire Mailer

An interface for php mail function.

### Installation

Via [composer](https://getcomposer.org)

```bash
composer require jetfirephp/mailer
```

Require SwiftMailer or PHPMailer 

```bash
composer require phpmailer/phpmailer
//or
composer require swiftmailer/swiftmailer
```

### Usage

```php
$config = [
    'transport' => 'smtp',
    'host' => 'localhost',
    'port' => 1025,
    'user' => '',
    'pass' => '',
];
$mailer = new JetFire\Mailer\SwiftMailer\SwiftMailer($config);
// or
// $mailer = new JetFire\Mailer\PhpMailer\PhpMailer($config); 

$mail = new JetFire\Mailer\Mail($mailer);

$mail->to('jet@fire.com')
    ->from('contact@fire.com')
    ->subject('Test')
    ->content('Test')
    ->send();
```

### License

The JetFire Mailer is released under the MIT public license : http://www.opensource.org/licenses/MIT. 