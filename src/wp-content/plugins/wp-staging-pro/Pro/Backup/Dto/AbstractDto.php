<?php

// TODO PHP7.x declare(strict_types=1);
// TODO PHP7.x type-hints & return types

namespace WPStaging\Pro\Backup\Dto;

use JsonSerializable;
use Serializable;
use WPStaging\Framework\Interfaces\ArrayableInterface;
use WPStaging\Framework\Traits\ArrayableTrait;
use WPStaging\Framework\Traits\HydrateTrait;

abstract class AbstractDto implements JsonSerializable, Serializable, ArrayableInterface
{
    use ArrayableTrait;
    use HydrateTrait;

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        $this->hydrate(unserialize($serialized));
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
