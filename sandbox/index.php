<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TimurFlush\Auth\Manager;

Manager::initialize();

var_dump(
    Manager::options('hashing')
);