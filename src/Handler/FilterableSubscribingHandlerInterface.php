<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;

/**
 * Implement this interface if you need to be able to filter your custom
 * handler based on the current (de)serialization context.
 *
 * @see SubscribingHandlerInterface
 */
interface FilterableSubscribingHandlerInterface extends SubscribingHandlerInterface
{
    /**
     * @param mixed $data    The data which needs to be (de)serialized
     * @param array $type    The type that needs to be (de)serialized
     * @param Context $context The (de)serialization context
     *
     * @return bool If true, skip this handler and use the default implementation
     */
    public function shouldBeSkipped($data, array $type, Context $context): bool;
}
