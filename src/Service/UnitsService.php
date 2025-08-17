<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Unit;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Unit create(array $params)
 * @method Unit|Unit[]|null get(int|array|null $params = null)
 * @method Unit update(int $id, array $params)
 * @method void delete(int $id)
 */
class UnitsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'units';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Unit();
    }
}
