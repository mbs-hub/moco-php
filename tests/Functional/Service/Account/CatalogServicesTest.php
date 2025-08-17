<?php

namespace Tests\Functional\Service\Account;

use Moco\Entity\Catalog;
use Moco\Entity\CatalogServiceItem;
use Tests\Functional\Service\AbstractServiceTest;

class CatalogServicesTest extends AbstractServiceTest
{
    private array $createParams = [
        'title' => 'Catalog entry',
        'items' => [
            [
                'type' => 'item',
                'title' => 'Setup',
                'net_total' => 1200.0,
            ]
        ]
    ];

    private array $item = [
        'type' => 'item',
        'title' => 'Catalog Service Item',
        'quantity' => 20.0,
        'unit' => 'h',
        'unit_price' => 150.0,
        'net_total' => 3000.0
    ];

    public function testCreate(): int
    {
        $catalog = $this->mocoClient->account->catalogServices->create($this->createParams);
        $this->assertInstanceOf(Catalog::class, $catalog);
        $this->assertEquals('Catalog entry', $catalog->title);
        $this->assertEquals('Setup', $catalog->items[0]->title);
        return $catalog->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $catalogId): int
    {
        $catalogs = $this->mocoClient->account->catalogServices->get();
        $this->assertIsArray($catalogs);

        $catalog = $this->mocoClient->account->catalogServices->get($catalogId);
        $this->assertEquals($catalogId, $catalog->id);
        return $catalogId;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $catalogId): int
    {
        $catalog = $this->mocoClient->account->catalogServices->update($catalogId, ['title' => 'new title']);
        $this->assertEquals('new title', $catalog->title);
        return $catalogId;
    }

    /**
     * @depends testUpdate
     */
    public function testCreateItem(int $catalogId): array
    {
        $item = $this->mocoClient->account->catalogServices->createItem($catalogId, $this->item);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals('Catalog Service Item', $item->title);

        return ['service_id' => $catalogId, 'id' => $item->id];
    }

    /**
     * @depends testCreateItem
     */
    public function testGetItem(array $data): array
    {
        $item = $this->mocoClient->account->catalogServices->getItem($data['service_id'], $data['id']);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals($data['id'], $item->id);
        return $data;
    }

    /**
     * @depends testGetItem
    */
    public function testUpdateItem(array $data): array
    {
        $data['title'] = 'item title changed';
        $item = $this->mocoClient->account->catalogServices->updateItem($data);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals($item->title, 'item title changed');
        return $data;
    }

    /**
     * @depends testUpdateItem
     */
    public function testDelete(array $data): void
    {
        $this->assertNull($this->mocoClient->account->catalogServices->deleteItem($data['service_id'], $data['id']));
        $this->assertNull($this->mocoClient->account->catalogServices->delete($data['service_id']));
    }
}
