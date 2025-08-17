<?php

declare(strict_types=1);

namespace Moco\Service\Projects;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\ProjectExpense;
use Moco\Service\AbstractService;
use Moco\Exception\InvalidRequestException;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

class ProjectExpensesService extends AbstractService
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
        return $this->endpoint . 'projects/' . $this->projectId . '/expenses';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new ProjectExpense();
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

    public function delete(int $projectId, int $expenseId): void
    {
        $this->projectId = $projectId;
        $this->traitDelete($expenseId);
    }

    public function createBulk(int $projectId, array $expenses): array
    {
        $this->projectId = $projectId;
        $params = ['expenses' => $expenses];
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndpoint() . '/bulk', $params);
        $result = json_decode($result);

        $entities = [];
        if (is_array($result)) {
            foreach ($result as $expenseData) {
                $entities[] = $this->createMocoEntity($expenseData, $this->getMocoObject());
            }
        }
        return $entities;
    }

    public function disregard(int $projectId, array $expenseIds, string $reason): void
    {
        $this->projectId = $projectId;
        $params = [
            'expense_ids' => $expenseIds,
            'reason' => $reason
        ];
        $params = $this->prepareParams($params);
        $this->client->request('POST', $this->getEndpoint() . '/disregard', $params);
    }
}
