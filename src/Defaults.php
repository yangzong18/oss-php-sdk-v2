<?php

declare(strict_types=1);

namespace AlibabaCloud\Oss\V2;

final class Defaults
{
    /**
     *Default transport 's connect timeout is 10, the unit is seconod 
     */
    const CONNECT_TIMEOUT = 10.0;

    /**
     *Default transport 's request timeout is 20, the unit is seconod
     */
    const READWRITE_TIMEOUT = 20.0;

    /**
     *Default signature version is v4
     */

    const SIGNATURE_VERSION = "v4";

    /**
     *Product for signing
     */
    const PRODUCT = "oss";

    /**
     *The URL's scheme, default is https
     */
    const ENDPOINT_SCHEME = "https";

    const MAX_ATTEMPTS = 3;
    const MAX_BACKOFF_S = 20.0;
    const BASE_DELAY_S = 0.2;
}
