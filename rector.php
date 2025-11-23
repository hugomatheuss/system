<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
        __DIR__ . '/database',
        __DIR__ . '/tests',
        __DIR__ . '/resources',
        __DIR__ . '/config',
    ]);

    // Definir target PHP se a classe e constante existirem (tornar resiliente a versões do rector)
    $phpVersionClass = '\\Rector\\Core\\ValueObject\\PhpVersion';
    $preferredPhpConsts = ['PHP_84', 'PHP_83', 'PHP_82', 'PHP_81'];
    if (class_exists($phpVersionClass)) {
        foreach ($preferredPhpConsts as $const) {
            $fullConst = $phpVersionClass . '::' . $const;
            if (defined($fullConst)) {
                // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedProperty
                $rectorConfig->phpVersion(constant($fullConst));
                break;
            }
        }
    }

    // Importar conjuntos de regras (sets) somente se estiverem disponíveis
    $sets = [];
    $setListClass = '\\Rector\\Set\\ValueObject\\SetList';
    if (class_exists($setListClass)) {
        foreach ([
            'DEAD_CODE',
            'CODE_QUALITY',
            'TYPE_DECLARATION',
            'EARLY_RETURN',
            'PRIVATIZATION',
        ] as $constName) {
            $fullConst = $setListClass . '::' . $constName;
            if (defined($fullConst)) {
                $sets[] = constant($fullConst);
            }
        }
    }

    // Regras específicas do Laravel (tentar importar o set do Laravel 12, 11 ou 10 se existir)
    $laravelSetClass = '\\Rector\\Laravel\\Set\\LaravelSetList';
    if (class_exists($laravelSetClass)) {
        foreach (['LARAVEL_120', 'LARAVEL_110', 'LARAVEL_100'] as $constName) {
            $fullConst = $laravelSetClass . '::' . $constName;
            if (defined($fullConst)) {
                $sets[] = constant($fullConst);
                break;
            }
        }
    }

    // Importar cada set individualmente (a API do Rector espera string por chamada)
    foreach ($sets as $set) {
        $rectorConfig->import($set);
    }

    // Importação automática de nomes (use statements) e classes curtas
    $rectorConfig->importNames(true);
    $rectorConfig->importShortClasses(false);

    // Habilita execução paralela quando possível
    $rectorConfig->parallel();

    // Ignorar pastas que não devem ser transformadas
    $rectorConfig->skip([
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/vendor',
        __DIR__ . '/node_modules',
        __DIR__ . '/public',
        __DIR__ . '/.git',
    ]);
};
