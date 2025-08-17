<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Schedule;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Schedule create(array $params)
 * @method Schedule|array|null get(int|array|null $params = null)
 * @method Schedule update(int $id, array $params)
 * @method void delete(int $id)
 */
class SchedulesService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'schedules';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Schedule();
    }
}
