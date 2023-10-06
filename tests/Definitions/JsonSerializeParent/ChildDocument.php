<?php

namespace Square\Pjson\Tests\Definitions\JsonSerializeParent;

use Square\Pjson\Json;

class ChildDocument
{
    use JsonSerializeParentTrait;

    #[Json('big_int')]
    public string $bigInt;
}
