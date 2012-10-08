<?php

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf('php "%s" cache:clear --env=%s --no-warmup', __DIR__.'/console', $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV']));
}

return require __DIR__.'/bootstrap.php.cache';
