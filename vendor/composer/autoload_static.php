<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit528708b26ffd3e16666b3ba7d8e68745
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phemail\\' => 8,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phemail\\' => 
        array (
            0 => __DIR__ . '/..' . '/vaibhavpandeyvpz/phemail/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit528708b26ffd3e16666b3ba7d8e68745::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit528708b26ffd3e16666b3ba7d8e68745::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}