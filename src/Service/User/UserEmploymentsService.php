<?php

namespace Moco\Service\User;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\UserEmployment;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method UserEmployment create(array $params)
 * @method UserEmployment|UserEmployment[]|null get(int|array|null $params = null)
 * @method UserEmployment update(int $id, array $params)
 * @method void delete(int $id)
 */
class UserEmploymentsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'users/employments';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new UserEmployment();
    }
}
