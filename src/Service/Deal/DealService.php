<?php

declare(strict_types=1);

namespace Moco\Service\Deal;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Activity;
use Moco\Entity\Deal;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Deal create(array $params)
 * @method Deal|Deal[]|null get(int|array|null $params = null)
 * @method Deal update(int $id, array $params)
 */
class DealService extends AbstractService
{
    use Create;
    use Get;
    use Update;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'deals';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Deal();
    }
}
