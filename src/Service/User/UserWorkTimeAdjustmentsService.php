<?php

namespace Moco\Service\User;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\UserWorkTimeAdjustment;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method UserWorkTimeAdjustment create(array $params)
 * @method UserWorkTimeAdjustment|array|null get(int|array|null $params = null)
 * @method UserWorkTimeAdjustment update(int $id, array $params)
 * @method void delete(int $id)
 */
class UserWorkTimeAdjustmentsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'users/work_time_adjustments';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new UserWorkTimeAdjustment();
    }
}
