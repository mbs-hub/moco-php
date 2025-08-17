<?php

namespace Moco\Service\Projects;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\ProjectPaymentSchedule;
use Moco\Exception\InvalidRequestException;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

class ProjectPaymentScheduleService extends AbstractService
{
    use Get {
        get as protected traitGet;
    }

    use Create {
        create as protected traitCreate;
    }

    use Update {
        update as protected traitUpdate;
    }

    use Delete {
        delete as protected traitDelete;
    }

    use SetProjectId;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'projects/' . $this->projectId . '/payment_schedules';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new ProjectPaymentSchedule();
    }

    public function get(array $params): AbstractMocoEntity|array|null
    {
        $this->setProjectId($params);
        if (isset($params['id']) && is_int($params['id'])) {
            $params = $params['id'];
        }
        return $this->traitGet($params);
    }

    public function create(array $params): AbstractMocoEntity
    {
        $this->setProjectId($params);
        unset($params['project_id']);
        return $this->traitCreate($params);
    }

    public function update(int $id, array $params): AbstractMocoEntity
    {
        $this->setProjectId($params);
        return $this->traitUpdate($id, $params);
    }

    public function delete(int $projectId, int $paymentScheduleId): void
    {
        $this->projectId = $projectId;
        $this->traitDelete($paymentScheduleId);
    }
}
