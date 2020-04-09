#!/usr/bin/env php
<?php

declare(strict_types=1);

use Hoa\Compiler\Llk\Llk;
use Hoa\File\Read;

@trigger_error(sprintf('This script, "%s" is deprecated and will be removed in the next major version.', __FILE__), E_USER_DEPRECATED);

require __DIR__ . '/../../vendor/autoload.php';

$compiler = Llk::load(new Read(__DIR__ . '/grammar.pp'));

file_put_contents(
    __DIR__ . '/InnerParser.php',
    <<<EOS
<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

/**
 * @internal
 * @generated Use regenerate-parser.php to refresh this class.
 */

EOS
    . 'final ' . Llk::save($compiler, 'InnerParser')
);
