<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbd99e94c162fdb2fb382898c56c1c8cf
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Reach\\RImage\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Reach\\RImage\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbd99e94c162fdb2fb382898c56c1c8cf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbd99e94c162fdb2fb382898c56c1c8cf::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
