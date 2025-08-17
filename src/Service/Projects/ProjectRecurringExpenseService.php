<?php

namespace Moco\Service\Projects;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\ProjectRecurringExpense;
use Moco\Exception\InvalidRequestException;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

class ProjectRecurringExpenseService extends AbstractService
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
        return $this->endpoint . 'projects/' . $this->projectId . '/recurring_expenses';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new ProjectRecurringExpense();
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

    public function delete(int $projectId, int $recurringExpenseId): void
    {
        $this->projectId = $projectId;
        $this->traitDelete($recurringExpenseId);
    }
}
