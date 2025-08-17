<?php

namespace Tests\Unit\Service\User;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class UserWorkTimeAdjustmentsServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'user_id' => 933590696,
        'description' => 'Overtime adjustment',
        'date' => '2022-01-01',
        'hours' => 8.5
    ];

    private array $responseData = [
        'id' => 123456789,
        'date' => '2022-01-01',
        'description' => 'Overtime adjustment',
        'hours' => 8.5,
        'creator' => [
            'id' => 933590696,
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        'user' => [
            'id' => 933590696,
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        'created_at' => '2022-01-01T10:00:00+01:00',
        'updated_at' => '2022-01-01T10:00:00+01:00'
    ];

    public function testCreate(): void
    {
        $this->mockResponse(200, json_encode($this->responseData));
        $adjustment = $this->mocoClient->userWorkTimeAdjustments->create($this->createParams);
        $this->assertEquals(123456789, $adjustment->id);
        $this->assertEquals('2022-01-01', $adjustment->date);
        $this->assertEquals('Overtime adjustment', $adjustment->description);
        $this->assertEquals(8.5, $adjustment->hours);
        $this->assertEquals(933590696, $adjustment->user->id);
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

    public function testCreateMissingDateException(): void
    {
        $params = $this->createParams;
        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userWorkTimeAdjustments->create($params);
    }

    public function testCreateMissingHoursException(): void
    {
        $params = $this->createParams;
        unset($params['hours']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userWorkTimeAdjustments->create($params);
    }

    public function testCreateInvalidResponseException(): void
    {
        $this->mockResponse(500);
        $this->expectException(InvalidResponseException::class);
        $this->mocoClient->userWorkTimeAdjustments->create($this->createParams);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->responseData));
        $adjustment = $this->mocoClient->userWorkTimeAdjustments->get(123456789);
        $this->assertEquals(123456789, $adjustment->id);
        $this->assertEquals('Overtime adjustment', $adjustment->description);

        $this->mockResponse(200, json_encode([$this->responseData]));
        $adjustments = $this->mocoClient->userWorkTimeAdjustments->get();
        $this->assertIsArray($adjustments);
        $this->assertEquals(123456789, $adjustments[0]->id);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->userWorkTimeAdjustments->get(99999);
    }

    public function testUpdate(): void
    {
        $updateParams = ['description' => 'Updated overtime', 'hours' => 10.0];
        $updatedData = array_merge($this->responseData, $updateParams);
        $this->mockResponse(200, json_encode($updatedData));

        $adjustment = $this->mocoClient->userWorkTimeAdjustments->update(123456789, $updateParams);
        $this->assertEquals('Updated overtime', $adjustment->description);
        $this->assertEquals(10.0, $adjustment->hours);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->userWorkTimeAdjustments->update(99999, $updateParams);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->userWorkTimeAdjustments->delete(123456789));
    }
}
