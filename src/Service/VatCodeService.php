<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\VatCode;
use Moco\Exception\InvalidRequestException;

class VatCodeService extends AbstractService
{
    public const TYPE_SALES = 'sales';
    public const TYPE_PURCHASES = 'purchases';

    private array $allowedTypes = [self::TYPE_SALES, self::TYPE_PURCHASES];

    private const ENDPOINT_PREFIX = 'vat_code_';

    protected function getEndpoint(): string
    {
        return $this->endpoint . self::ENDPOINT_PREFIX;
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new VatCode();
    }

    private function validateType(string $type): void
    {
        if (!in_array($type, $this->allowedTypes)) {
            throw new InvalidRequestException(
                'Invalid VAT code type. Allowed types: ' . self::TYPE_SALES . ', ' . self::TYPE_PURCHASES
            );
        }
    }

    private function get(string $type, array $filters = []): array
    {
        $this->validateType($type);

        $queryParams = $this->prepareQueryParams($filters);
        $endpoint = $this->getEndpoint() . $type . $queryParams;

        $result = $this->client->request('GET', $endpoint);
        $data = json_decode($result);

        if (!is_array($data)) {
            return [];
        }

        $entities = [];
        foreach ($data as $item) {
            $entities[] = $this->createMocoEntity($item, $this->getMocoObject());
        }

        return $entities;
    }

    private function getById(string $type, int $id): VatCode
    {
        $this->validateType($type);

        $endpoint = $this->getEndpoint() . $type . '/' . $id;
        $result = $this->client->request('GET', $endpoint);
        $data = json_decode($result);

        return $this->createMocoEntity($data, $this->getMocoObject());
    }

    public function getSales(array $filters = []): array
    {
        return $this->get(self::TYPE_SALES, $filters);
    }

    public function getPurchases(array $filters = []): array
    {
        return $this->get(self::TYPE_PURCHASES, $filters);
    }

    public function getSale(int $id): VatCode
    {
        return $this->getById(self::TYPE_SALES, $id);
    }

    public function getPurchase(int $id): VatCode
    {
        return $this->getById(self::TYPE_PURCHASES, $id);
    }
}
