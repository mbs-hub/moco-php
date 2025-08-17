<?php

namespace Tests\Unit\Service\Account;

use Moco\Entity\CatalogServiceItem;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class CatalogServicesTest extends AbstractServiceTest
{
    private array $createParams = [
        'id' => 123,
        'title' => 'catalog title',
        'items' => [
            [
                'type' => 'item',
                'title' => 'item1',
                'description' => 'description',
                'quantity' => 1.0,
                'unit' => 'm',
                'unit_price' => 1.5,
                'net_total' => 1100.0,
                'unit_cost' => 0.5,
                'optional' => false,
                'part' => false,
                'additional' => false,
            ]
        ]
    ];

    private array $item = [
        'id' => 1234,
        'type' => 'item',
        'title' => 'Catalog Service Item',
        'quantity' => 20.0,
        'unit' => 'h',
        'unit_price' => 150.0,
        'net_total' => 3000.0
    ];

    public function testCreate(): void
    {
        $this->mockResponse(200, json_encode($this->createParams));
        $catalog = $this->mocoClient->account->catalogServices->create($this->createParams);
        $this->assertEquals('catalog title', $catalog->title);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->account->catalogServices->create([]);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->createParams]));
        $catalogs = $this->mocoClient->account->catalogServices->get();
        $this->assertIsArray($catalogs);
        $this->assertEquals(123, $catalogs[0]->id);

        $this->mockResponse(200, json_encode($this->createParams));
        $catalog = $this->mocoClient->account->catalogServices->get(123);
        $this->assertEquals('catalog title', $catalog->title);

        $this->mockResponse(404, '');
        $this->expectException(NotFoundException::class);
        $this->mocoClient->account->catalogServices->get(1234);
    }

    public function testUpdate(): void
    {
        $updateResponse = $this->createParams;
        $updateResponse['title'] = 'titleChanged';
        $this->mockResponse(200, json_encode($updateResponse));
        $catalog = $this->mocoClient->account->catalogServices->update(123, ['title' => 'titleChanged']);
        $this->assertEquals('titleChanged', $catalog->title);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204, '');
        $this->assertNull($this->mocoClient->account->catalogServices->delete(123));
    }

    public function testCreateItem(): void
    {
        $this->mockResponse(200, json_encode($this->item));
        $item = $this->mocoClient->account->catalogServices->createItem(123, $this->item);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals(1234, $item->id);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->account->catalogServices->createItem(123, []);
    }

    public function testUpdateItem(): void
    {
        $updateParam = $this->item;
        $updateParam['title'] = 'item title changed';
        $updateParam['id'] = 1234;
        $updateParam['service_id'] = 123;
        $this->mockResponse(200, json_encode($updateParam));
        $item = $this->mocoClient->account->catalogServices->updateItem($updateParam);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals('item title changed', $item->title);

        unset($updateParam['id']);
        unset($updateParam['service_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->account->catalogServices->updateItem($updateParam);
    }

    public function testGetItem(): void
    {
        $this->mockResponse(200, json_encode($this->item));
        $item = $this->mocoClient->account->catalogServices->getItem(123, 1234);
        $this->assertInstanceOf(CatalogServiceItem::class, $item);
        $this->assertEquals(1234, $item->id);
    }

    public function testDeleteItem(): void
    {
        $this->mockResponse(204, '');
        $this->assertNull($this->mocoClient->account->catalogServices->deleteItem(123, 1234));
    }
}
