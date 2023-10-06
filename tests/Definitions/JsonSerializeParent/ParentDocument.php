<?php

namespace Square\Pjson\Tests\Definitions\JsonSerializeParent;

use Square\Pjson\Json;

class ParentDocument
{
    use JsonSerializeParentTrait;

    #[Json]
    public ChildDocument $childDocument;
}
