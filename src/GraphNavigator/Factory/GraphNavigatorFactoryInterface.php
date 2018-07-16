<?php

declare(strict_types=1);

namespace JMS\Serializer\GraphNavigator\Factory;

use JMS\Serializer\GraphNavigatorInterface;

interface GraphNavigatorFactoryInterface
{
    public function getGraphNavigator(): GraphNavigatorInterface;
}
