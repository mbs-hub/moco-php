<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectContract;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectContractsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "user_id" => 933590696,
        "firstname" => "John",
        "lastname" => "Doe",
        "billable" => true,
        "active" => true,
        "budget" => 9900.0,
        "hourly_rate" => 120.0,
        "created_at" => "2024-01-10T09:00:00Z",
        "updated_at" => "2024-01-10T09:00:00Z"
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetAll(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $contracts = $this->mocoClient->projectContracts->get(['project_id' => 123456]);
        $this->assertIsArray($contracts);
        $this->assertCount(1, $contracts);
        $this->assertInstanceOf(ProjectContract::class, $contracts[0]);
        $this->assertEquals(12345, $contracts[0]->id);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $contract = $this->mocoClient->projectContracts->get(['project_id' => 123456, 'id' => 12345]);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertEquals(12345, $contract->id);
        $this->assertEquals(933590696, $contract->user_id);
        $this->assertEquals("John", $contract->firstname);
        $this->assertEquals("Doe", $contract->lastname);
        $this->assertTrue($contract->billable);
        $this->assertTrue($contract->active);
        $this->assertEquals(9900.0, $contract->budget);
        $this->assertEquals(120.0, $contract->hourly_rate);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectContracts->get(['project_id' => 123456, 'id' => 999999]);
    }

    public function testCreate(): void
    {
        $createData = [
            'project_id' => 123456,
            'user_id' => 933590696,
            'billable' => true,
            'active' => true,
            'budget' => 9900.0,
            'hourly_rate' => 120.0
        ];

        $this->mockResponse(201, json_encode($this->expectedResponse));
        $contract = $this->mocoClient->projectContracts->create($createData);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertEquals(12345, $contract->id);
        $this->assertEquals(933590696, $contract->user_id);
        $this->assertTrue($contract->billable);
        $this->assertEquals(9900.0, $contract->budget);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'project_id' => 123456,
            'budget' => 12000.0,
            'hourly_rate' => 150.0,
            'billable' => false
        ];

        $updatedResponse = $this->expectedResponse;
        $updatedResponse['budget'] = 12000.0;
        $updatedResponse['hourly_rate'] = 150.0;
        $updatedResponse['billable'] = false;

        $this->mockResponse(200, json_encode($updatedResponse));
        $contract = $this->mocoClient->projectContracts->update(12345, $updateData);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertEquals(12345, $contract->id);
        $this->assertEquals(12000.0, $contract->budget);
        $this->assertEquals(150.0, $contract->hourly_rate);
        $this->assertFalse($contract->billable);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->mocoClient->projectContracts->delete(123456, 12345);
        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testCreateMinimal(): void
    {
        $createData = [
            'project_id' => 123456,
            'user_id' => 933590696
        ];

        $minimalResponse = [
            "id" => 12346,
            "user_id" => 933590696,
            "firstname" => "Jane",
            "lastname" => "Smith",
            "billable" => true,
            "active" => true,
            "budget" => null,
            "hourly_rate" => null,
            "created_at" => "2024-01-10T09:00:00Z",
            "updated_at" => "2024-01-10T09:00:00Z"
        ];

        $this->mockResponse(201, json_encode($minimalResponse));
        $contract = $this->mocoClient->projectContracts->create($createData);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertEquals(12346, $contract->id);
        $this->assertEquals(933590696, $contract->user_id);
        $this->assertNull($contract->budget);
        $this->assertNull($contract->hourly_rate);
    }
}
