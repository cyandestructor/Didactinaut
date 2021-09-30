<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit395d085166ce992cdc0b6c7f2ee46096
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Small\\Middleware\\' => 17,
            'Small\\Interfaces\\' => 17,
            'Small\\Http\\' => 11,
            'Small\\Core\\' => 11,
        ),
        'D' => 
        array (
            'Didactinaut\\Models\\Dto\\' => 23,
            'Didactinaut\\Models\\' => 19,
            'Didactinaut\\Factories\\Database\\' => 31,
            'Didactinaut\\Didactinaut\\' => 24,
            'Didactinaut\\Controllers\\' => 24,
            'Didactinaut\\Configuration\\Database\\' => 35,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Small\\Middleware\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Small/Middleware',
        ),
        'Small\\Interfaces\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Small/Interfaces',
        ),
        'Small\\Http\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Small/Http',
        ),
        'Small\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Small/Core',
        ),
        'Didactinaut\\Models\\Dto\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Models/Dto',
        ),
        'Didactinaut\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Models',
        ),
        'Didactinaut\\Factories\\Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Factories/Database',
        ),
        'Didactinaut\\Didactinaut\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Didactinaut\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Controllers',
        ),
        'Didactinaut\\Configuration\\Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Configuration/Database',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit395d085166ce992cdc0b6c7f2ee46096::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit395d085166ce992cdc0b6c7f2ee46096::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit395d085166ce992cdc0b6c7f2ee46096::$classMap;

        }, null, ClassLoader::class);
    }
}
