<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Core\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    if (class_exists(PhpVersion::class)) {
        // PHP 8.4 may not be present as a constant in Rector yet; usar 8.2 como fallback
        if (defined(PhpVersion::class . '::PHP_84')) {
            $rectorConfig->phpVersion(PhpVersion::PHP_84);
        } elseif (defined(PhpVersion::class . '::PHP_83')) {
            $rectorConfig->phpVersion(PhpVersion::PHP_83);
        } else {
            $rectorConfig->phpVersion(PhpVersion::PHP_82);
        }
    }

    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
    ]);

    $sets = [
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
    ];

    // If driftingly/rector-laravel is installed, try to include its Laravel set(s)
    if (class_exists('Drifting\\RectorLaravel\\Set\\LaravelSetList')) {
        $laravelSet = 'Drifting\\RectorLaravel\\Set\\LaravelSetList';
        // prefer an explicit Laravel 12 set if available
        if (defined($laravelSet . '::LARAVEL_12')) {
            $sets[] = $laravelSet . '::LARAVEL_12';
        } elseif (defined($laravelSet . '::UPGRADE_TO_LARAVEL_12')) {
            $sets[] = $laravelSet . '::UPGRADE_TO_LARAVEL_12';
        } else {
            // fallback to applying the whole set class if it provides constants used by ->sets
            $sets[] = $laravelSet;
        }
    }

    $rectorConfig->sets($sets);

    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
        __DIR__ . '/node_modules',
    ]);
};