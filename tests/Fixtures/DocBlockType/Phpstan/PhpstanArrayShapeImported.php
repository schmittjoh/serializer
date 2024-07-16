<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan;

use JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan\Sub\PhpstanArrayShapeToImport;

/**
 * @phpstan-import-type Settings from PhpstanArrayShapeToImport
 * @phpstan-import-type Settings from PhpstanArrayShapeToImport as AliasedSettings
 */
final class PhpstanArrayShapeImported
{
    /**
     * @var Settings
     */
    public $data;

    /**
     * @var AliasedSettings
     */
    public $dataAliased;
}
