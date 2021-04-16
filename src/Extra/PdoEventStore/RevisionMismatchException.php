<?php

declare(strict_types=1);

namespace BusFactor\Extra\PdoEventStore;

use BusFactor\EventStore\EventStoreException;

final class RevisionMismatchException extends EventStoreException
{
}
