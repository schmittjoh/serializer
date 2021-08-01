<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Util;

class DeprecationLogger
{
    private $errors = [];

    /**
     * @var DeprecationLogger
     */
    private static $instance;

    private function __construct()
    {
    }

    public function __destruct()
    {
        if (!count($this->errors)) {
            return;
        }

        sort($this->errors);
        echo sprintf('Detected %s deprecations:', count($this->errors)) . PHP_EOL;
        array_map(static function (array $m) {
            [$errno, $errstr, $errfile, $errline] = $m;
            echo '- ' . sprintf('%s in %s:%s', $errstr, $errfile, $errline) . PHP_EOL;
        }, $this->errors);
    }

    public static function register(): void
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
            self::$instance->errors[] = [$errno, $errstr, $errfile, $errline];
        }, E_DEPRECATED);
    }
}
