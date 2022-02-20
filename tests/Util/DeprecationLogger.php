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

        $showDeprecations = getenv('JMS_TESTS_SHOW_DEPRECATIONS');
        echo sprintf('Detected %s deprecations.', count($this->errors)) . (!$showDeprecations  ? ' ' : PHP_EOL);
        if (!$showDeprecations) {
            echo sprintf('Set the env variable JMS_TESTS_SHOW_DEPRECATIONS to 1 if you want to see the full deprecations list.') . PHP_EOL;
        } else {
            sort($this->errors);
            array_map(static function (array $m) {
                [$errno, $errstr, $errfile, $errline] = $m;
                echo '- ' . sprintf('%s in %s:%s', $errstr, $errfile, $errline) . PHP_EOL;
            }, $this->errors);
        }
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
