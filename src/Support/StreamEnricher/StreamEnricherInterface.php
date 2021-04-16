<?php

declare(strict_types=1);

namespace BusFactor\Support\StreamEnricher;

use BusFactor\Aggregate\Stream;

interface StreamEnricherInterface
{
    public function enrich(Stream $stream): Stream;
}
