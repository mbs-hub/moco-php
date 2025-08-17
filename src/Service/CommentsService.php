<?php

namespace Moco\Service;

use Moco\Entity\Comment;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Comment create(array $params)
 * @method Comment|Comment[]|null get(int|array|null $params = null)
 * @method Comment update(int $id, array $params)
 * @method void delete(int $id)
 */
class CommentsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . '/comments';
    }

    protected function getMocoObject(): Comment
    {
        return new Comment();
    }

    /**
     * @return Comment[]|Comment
     */
    public function bulkCreate(array $params): array|Comment
    {
        $mandatoryParams = ['commentable_ids', 'commentable_type', 'text'];
        $this->validateParams($mandatoryParams, $params);
        $params = $this->prepareParams($params);
        $result = $this->client->request('POST', $this->getEndPoint() . '/bulk', $params);

        $result = json_decode($result);
        if (is_array($result)) {
            $entities = [];
            foreach ($result as $entity) {
                $entities[] = $this->createMocoEntity($entity, $this->getMocoObject());
            }

            return $entities;
        } else {
            return $this->createMocoEntity($result, $this->getMocoObject());
        }
    }
}
