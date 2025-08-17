<?php

declare(strict_types=1);

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\PlanningEntry;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

class PlanningEntriesService extends AbstractService
{
    use Create;
    use Delete;
    use Get;
    use Update;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'planning_entries';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new PlanningEntry();
    }
}
