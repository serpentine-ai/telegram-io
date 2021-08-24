# Telegram IO

```
composer require serpentine/telegram-io
```

```php
<?php

use Serpentine\IO\Telegram;

$telegram = new Telegram ('api token');
```

```php
<?php

use Serpentine\IO\Telegram;
use Serpentine\Config;

$telegram = new Telegram (Config::get ('pipeline.telegram.apiToken'));
```