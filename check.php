<?php

declare(strict_types=1);

ksort($_ENV);
print_r($_ENV);
ksort($_SERVER);
print_r($_SERVER);

print_r($argv);
