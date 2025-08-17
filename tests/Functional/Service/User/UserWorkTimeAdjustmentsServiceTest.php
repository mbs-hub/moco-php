<?php

namespace Tests\Functional\Service\User;

use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class UserWorkTimeAdjustmentsServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'user_id' => 933736920,
        'description' => 'Functional test overtime adjustment',
        'date' => '2023-01-01',
        'hours' => 6.0
    ];

    public function testCreate(): int
    {
        $this->createParams['date'] = date('Y-m-d', strtotime('-7 days'));
        $this->createParams['description'] = 'Test adjustment ' . time();

        $adjustment = $this->mocoClient->userWorkTimeAdjustments->create($this->createParams);
        $this->assertEquals($this->createParams['description'], $adjustment->description);
        $this->assertEquals($this->createParams['date'], $adjustment->date);
        $this->assertEquals($this->createParams['hours'], $adjustment->hours);
        $this->assertIsInt($adjustment->id);

        return $adjustment->id;
    }

    public function testCreateMissingUserIdException(): void
    {
        $params = $this->createParams;
        unset($params['user_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userWorkTimeAdjustments->create($params);
    }

    public function testCreateMissingDescriptionException(): void
    {
        $params = $this->createParams;
        unset($params['description']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userWorkTimeAdjustments->create($params);
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $adjustmentId): int
    {
        $adjustment = $this->mocoClient->userWorkTimeAdjustments->get($adjustmentId);
        $this->assertEquals($adjustmentId, $adjustment->id);
        $this->assertIsString($adjustment->description);
        $this->assertIsString($adjustment->date);
        $this->assertIsFloat($adjustment->hours);

        $adjustments = $this->mocoClient->userWorkTimeAdjustments->get();
        $this->assertIsArray($adjustments);
        $this->assertNotEmpty($adjustments);

        return $adjustmentId;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $adjustmentId): int
    {
        $updateParams = [
            'description' => 'Updated functional test adjustment',
            'hours' => 8.0
        ];

        $adjustment = $this->mocoClient->userWorkTimeAdjustments->update($adjustmentId, $updateParams);
        $this->assertEquals($updateParams['description'], $adjustment->description);
        $this->assertEquals($updateParams['hours'], $adjustment->hours);

        return $adjustmentId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $adjustmentId): void
    {
        $this->assertNull($this->mocoClient->userWorkTimeAdjustments->delete($adjustmentId));
    }
}
