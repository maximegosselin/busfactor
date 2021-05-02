<?php

declare(strict_types=1);

namespace BusFactor\StreamEnricher;

use BusFactor\Aggregate\Stream;

interface StreamEnricherInterface
{
    public function enrich(Stream $stream): Stream;
}
