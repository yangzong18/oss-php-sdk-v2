<?php

namespace OSS\Signer;

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
