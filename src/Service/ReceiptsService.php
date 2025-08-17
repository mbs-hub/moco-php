<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Receipt;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Receipt create(array $params)
 * @method Receipt|array|null get(int|array|null $params = null)
 * @method Receipt update(int $id, array $params)
 * @method void delete(int $id)
 */
class ReceiptsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'receipts';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Receipt();
    }
}
