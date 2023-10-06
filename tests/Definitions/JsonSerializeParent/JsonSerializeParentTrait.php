<?php

namespace Square\Pjson\Tests\Definitions\JsonSerializeParent;

use Square\Pjson\JsonSerialize;

trait JsonSerializeParentTrait
{
    use JsonSerialize{
        toJson as toJsonOriginal;
    }

    public function toJson(int $flags = 0, int $depth = 512): string
    {
        // Do al kind of crazy things here..
        return $this->toJsonOriginal($flags, $depth);
    }
}
