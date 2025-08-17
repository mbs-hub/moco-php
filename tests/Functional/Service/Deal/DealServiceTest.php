<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Deal;

use Functional\Service\User\UsersServiceTest;
use Moco\Entity\Deal;
use Tests\Functional\Service\AbstractServiceTest;

class DealServiceTest extends AbstractServiceTest
{
    public function testCreate(): int
    {
        $userServiceTest = new UsersServiceTest();
        $userId = $userServiceTest->testCreate();

        $dealCategoryService = new DealCategoryServiceTest();

        $dealCategoryId = $dealCategoryService->testCreate();
        $params = [
            'name' => 'New deal',
            'currency' => 'EUR',
            'money' => 12000,
            'reminder_date' => '2017-08-15',
            'user_id' => $userId,
            'deal_category_id' => $dealCategoryId
        ];
        $deal = $this->mocoClient->deal->create($params);
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertEquals('New deal', $deal->name);
        return $deal->id;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(int $dealId): int
    {
        $deal = $this->mocoClient->deal->update($dealId, ['name' => 'new name']);
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertEquals('new name', $deal->name);
        return $deal->id;
    }

    /**
     * @depends testUpdate
     */
    public function testGet(int $dealId): void
    {
        $deal = $this->mocoClient->deal->get($dealId);
        $this->assertInstanceOf(Deal::class, $deal);
        $this->assertEquals($dealId, $deal->id);

        $deals = $this->mocoClient->deal->get();
        $this->assertIsArray($deals);
    }
}
