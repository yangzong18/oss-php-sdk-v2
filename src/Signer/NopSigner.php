<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2\Signer;

class NopSigner implements Signer
{
    public function sign(SigningContext $signingCtx)
    {
        return null;
    }

    public function preSign(SigningContext $signingCtx)
    {
        return null;
    }
}
