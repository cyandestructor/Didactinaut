<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit395d085166ce992cdc0b6c7f2ee46096
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        'c964ee0ededf28c96ebd9db5099ef910' => __DIR__ . '/..' . '/guzzlehttp/promises/src/functions_include.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Small\\Middleware\\' => 17,
            'Small\\Interfaces\\' => 17,
            'Small\\Http\\' => 11,
            'Small\\Core\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'M' => 
        array (
            'MicrosoftAzure\\Storage\\Common\\' => 30,
            'MicrosoftAzure\\Storage\\Blob\\' => 28,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
        'D' => 
        array (
            'Didactinaut\\Validators\\' => 23,
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
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'MicrosoftAzure\\Storage\\Common\\' => 
        array (
            0 => __DIR__ . '/..' . '/microsoft/azure-storage-common/src/Common',
        ),
        'MicrosoftAzure\\Storage\\Blob\\' => 
        array (
            0 => __DIR__ . '/..' . '/microsoft/azure-storage-blob/src/Blob',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
        'Didactinaut\\Validators\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Didactinaut/Validators',
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
