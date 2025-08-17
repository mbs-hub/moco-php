<?php

namespace Moco\Service\User;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\User;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method User create(array $params)
 * @method User|array|null get(int|array|null $params = null)
 * @method User update(int $id, array $params)
 * @method void delete(int $id)
 */
class UsersService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    public function getEndPoint(): string
    {
        return $this->endpoint . 'users';
    }

    public function getMocoObject(): AbstractMocoEntity
    {
        return new User();
    }

    public function getPerformanceReport(int $userId, array $params = null): object
    {
        $urlQuery = '';
        if (is_array($params)) {
            $urlQuery = '?' . http_build_query($params);
        }
        $endpoint = $this->getEndPoint() . '/' . $userId . '/performance_report' . $urlQuery;
        $result = $this->client->request("GET", $endpoint);

        return json_decode($result);
    }
}
