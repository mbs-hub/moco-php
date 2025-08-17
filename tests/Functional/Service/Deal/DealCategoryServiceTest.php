<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Deal;

use Moco\Entity\DealCategory;
use Tests\Functional\Service\AbstractServiceTest;

class DealCategoryServiceTest extends AbstractServiceTest
{
    private array $params = [
        'name' => 'new deal category',
        'probability' => 15
    ];

    public function testCreate(): int
    {
        $params = [
            'name' => 'new deal category' . time(),
            'probability' => 15
        ];
        $dealCategory = $this->mocoClient->dealCategory->create($params);
        $this->assertInstanceOf(DealCategory::class, $dealCategory);
        $this->assertEquals($params['name'], $dealCategory->name);
        return $dealCategory->id;
    }

    /**
     * @depends testCreate
     */
    public function testGetDealCategories(): void
    {
        $deals = $this->mocoClient->dealCategory->get();
        $this->assertIsArray($deals);
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $dealId): int
    {
        $deal = $this->mocoClient->dealCategory->get($dealId);
        $this->assertInstanceOf(DealCategory::class, $deal);
        $this->assertEquals($dealId, $deal->id);
        return $deal->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $dealId): int
    {
        $param = ['name' => 'updated' . time()];
        $deal = $this->mocoClient->dealCategory->update($dealId, $param);
        $this->assertInstanceOf(DealCategory::class, $deal);
        $this->assertEquals($param['name'], $deal->name);
        return $deal->id;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $dealId): void
    {
        $this->assertNull($this->mocoClient->dealCategory->delete($dealId));
    }
}
