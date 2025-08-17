<?php

namespace Moco\Service\User;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\UserPresence;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method UserPresence create(array $params)
 * @method UserPresence|array|null get(int|array|null $params = null)
 * @method UserPresence update(int $id, array $params)
 * @method void delete(int $id)
 */
class UserPresencesService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'users/presences';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new UserPresence();
    }

    public function touch(): UserPresence
    {
        $result = $this->client->request('POST', $this->getEndpoint() . '/touch');
        return $this->createMocoEntity(json_decode($result), $this->getMocoObject());
    }
}
