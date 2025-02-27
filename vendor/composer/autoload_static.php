<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit206def9034dffb1026ad8498f6a5d853
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/../..' . '/app',
    );

    public static $classMap = array (
        'FPDM' => __DIR__ . '/..' . '/tmw/fpdm/src/fpdm.php',
        'FilterASCII85' => __DIR__ . '/..' . '/tmw/fpdm/src/filters/FilterASCII85.php',
        'FilterASCIIHex' => __DIR__ . '/..' . '/tmw/fpdm/src/filters/FilterASCIIHex.php',
        'FilterFlate' => __DIR__ . '/..' . '/tmw/fpdm/src/filters/FilterFlate.php',
        'FilterLZW' => __DIR__ . '/..' . '/tmw/fpdm/src/filters/FilterLZW.php',
        'FilterStandard' => __DIR__ . '/..' . '/tmw/fpdm/src/filters/FilterStandard.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit206def9034dffb1026ad8498f6a5d853::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit206def9034dffb1026ad8498f6a5d853::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit206def9034dffb1026ad8498f6a5d853::$fallbackDirsPsr4;
            $loader->classMap = ComposerStaticInit206def9034dffb1026ad8498f6a5d853::$classMap;

        }, null, ClassLoader::class);
    }
}
