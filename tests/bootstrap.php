<?php

use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$envfile = __DIR__ . '/../.env';
if (file_exists($envfile)) {
    (new Dotenv())
        ->usePutenv()
        ->load($envfile);
}
