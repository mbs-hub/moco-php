<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Activity;
use Moco\Exception\InvalidRequestException;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Activity create(array $params)
 * @method Activity|Activity[]|null get(int|array|null $params = null)
 * @method Activity update(int $id, array $params)
 * @method void delete(int $id)
 */
class ActivitiesService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'activities';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Activity();
    }

    public function startTimer(int $activityId): Activity
    {
        $endpoint = $this->getEndpoint() . '/' . $activityId . '/start_timer';
        $result  = $this->client->request('PATCH', $endpoint);
        return $this->createMocoEntity(json_decode($result), $this->getMocoObject());
    }

    public function stopTimer(int $activityId): Activity
    {
        $endpoint = $this->getEndpoint() . '/' . $activityId . '/stop_timer';
        $result  = $this->client->request('PATCH', $endpoint);
        return $this->createMocoEntity(json_decode($result), $this->getMocoObject());
    }

    public function disregard(array $params): array
    {
        $mandatoryParams = ['reason', 'activity_ids', 'company_id'];
        $this->validateParams($mandatoryParams, $params);
        $endpoint = $this->getEndpoint() . '/disregard';
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $endpoint, $params);
        return json_decode($result);
    }
}
