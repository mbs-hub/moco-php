<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Tag;
use Moco\Exception\InvalidRequestException;

class TagsService extends AbstractService
{
    private array $supportedEntities = [
        'Company', 'Contact', 'Project', 'Deal',
        'Purchase', 'Invoice', 'Offer', 'User'
    ];

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'taggings';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Tag();
    }

    private function validateEntity(string $entity): void
    {
        if (!in_array($entity, $this->supportedEntities)) {
            throw new InvalidRequestException(
                'Invalid entity type. Supported entities: ' . implode(', ', $this->supportedEntities)
            );
        }
    }

    public function getTags(string $entity, int $entityId): array
    {
        $this->validateEntity($entity);

        $result = $this->client->request(
            'GET',
            $this->getEndpoint() . '/' . $entity . '/' . $entityId
        );
        return json_decode($result, true) ?: [];
    }

    public function addTags(string $entity, int $entityId, array $tags): void
    {
        $this->validateEntity($entity);

        $this->client->request(
            'PATCH',
            $this->getEndpoint() . '/' . $entity . '/' . $entityId,
            $tags
        );
    }

    public function replaceTags(string $entity, int $entityId, array $tags): void
    {
        $this->validateEntity($entity);

        $this->client->request(
            'PUT',
            $this->getEndpoint() . '/' . $entity . '/' . $entityId,
            $tags
        );
    }

    public function removeTags(string $entity, int $entityId, array $tags): void
    {
        $this->validateEntity($entity);

        $this->client->request(
            'DELETE',
            $this->getEndpoint() . '/' . $entity . '/' . $entityId,
            $tags
        );
    }
}
