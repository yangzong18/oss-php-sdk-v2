<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Signer;

interface SignerInterface
{
    public function sign(SigningContext $signingCtx);
}
