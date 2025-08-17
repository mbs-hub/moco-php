<?php

namespace Moco\Service\Account;

use Moco\Entity\Catalog;
use Moco\Entity\CatalogServiceItem;
use Moco\Entity\AbstractMocoEntity;
use Moco\Exception\InvalidRequestException;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method Catalog create(array $params)
 * @method Catalog|Catalog[]|null get(int|array|null $params = null)
 * @method Catalog update(int $id, array $params)
 * @method void delete(int $id)
 */
class CatalogServices extends AbstractService
{
    use Create;
    use Get;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'account/catalog_services';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Catalog();
    }

    public function getItem(int $serviceId, int $itemId): CatalogServiceItem
    {
        $result = $this->client->request('GET', $this->getEndpoint() . '/' . $serviceId . '/items/' . $itemId);
        $result = json_decode($result);
        return $this->createMocoEntity($result, new CatalogServiceItem());
    }

    public function createItem(int $serviceId, array $params): CatalogServiceItem
    {
        $mandatoryFields = new CatalogServiceItem();
        $this->validateParams($mandatoryFields->getMandatoryFields(), $params);
        $params = $this->prepareParams($params);
        $endpoint = $this->getEndpoint() . '/' . $serviceId . '/items';
        $result = $this->client->request('POST', $endpoint, $params);

        return $this->createMocoEntity(json_decode($result), new CatalogServiceItem());
    }

    public function updateItem(array $params): CatalogServiceItem
    {
        if (empty($params['service_id']) || empty($params['id'])) {
            throw new InvalidRequestException('please provide value for service_id and id');
        }
        $endpoint = $this->getEndpoint() . '/' . $params['service_id'] . '/items/' . $params['id'];
        unset($params['service_id']);
        unset($params['id']);
        $params = $this->prepareParams($params);
        $result = $this->client->request("PUT", $endpoint, $params);

        return $this->createMocoEntity(json_decode($result), new CatalogServiceItem());
    }

    public function deleteItem(int $serviceId, int $itemId): void
    {
        $endpoint = $this->getEndpoint() . '/' . $serviceId . '/items/' . $itemId;
        $this->client->request("DELETE", $endpoint);
    }
}
