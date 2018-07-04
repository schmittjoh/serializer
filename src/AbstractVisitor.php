<?php

declare(strict_types=1);

namespace JMS\Serializer;

/**
 * @internal
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     * @var GraphNavigatorInterface
     */
    protected $navigator;

    public function setNavigator(GraphNavigatorInterface $navigator): void
    {
        $this->navigator = $navigator;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        return $data;
    }

    protected function getElementType(array $typeArray): ?array
    {
        if (false === isset($typeArray['params'][0])) {
            return null;
        }

        if (isset($typeArray['params'][1]) && \is_array($typeArray['params'][1])) {
            return $typeArray['params'][1];
        } else {
            return $typeArray['params'][0];
        }
    }
}
