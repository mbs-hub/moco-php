<?php

namespace Moco\Service\User;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\UserHoliday;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method UserHoliday create(array $params)
 * @method UserHoliday|UserHoliday[]|null get(int|array|null $params = null)
 * @method UserHoliday update(int $id, array $params)
 * @method void delete(int $id)
 */
class UserHolidaysService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'users/holidays';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new UserHoliday();
    }
}
