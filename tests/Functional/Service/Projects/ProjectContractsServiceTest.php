<?php

declare(strict_types=1);

namespace Functional\Service\Projects;

use Moco\Entity\ProjectContract;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectContractsServiceTest extends AbstractServiceTest
{
    private int $testProjectId = 947556944; // This should be replaced with a valid project ID
    private int $testUserId = 933736932; // This should be replaced with a valid user ID

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreate(): int
    {
        $createData = [
            'project_id' => $this->testProjectId,
            'user_id' => $this->testUserId,
            'billable' => true,
            'active' => true,
            'budget' => 15000.0,
            'hourly_rate' => 125.0
        ];

        $contract = $this->mocoClient->projectContracts->create($createData);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertNotNull($contract->id);
        $this->assertEquals($this->testUserId, $contract->user_id);
        $this->assertTrue($contract->billable);
        $this->assertTrue($contract->active);
        $this->assertEquals(15000.0, $contract->budget);
        $this->assertEquals(125.0, $contract->hourly_rate);
        $this->assertNotNull($contract->created_at);
        $this->assertNotNull($contract->updated_at);

        return $contract->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $contractId): int
    {
        $contract = $this->mocoClient->projectContracts->get(['project_id' => $this->testProjectId, 'id' => $contractId]);
        $this->assertInstanceOf(ProjectContract::class, $contract);
        $this->assertEquals($contractId, $contract->id);
        $this->assertEquals($this->testUserId, $contract->user_id);
        $this->assertTrue($contract->billable);
        $this->assertTrue($contract->active);
        $this->assertEquals(15000.0, $contract->budget);
        $this->assertEquals(125.0, $contract->hourly_rate);

        return $contractId;
    }

    /**
     * @depends testGet
     */
    public function testGetAll(int $contractId): int
    {
        $contracts = $this->mocoClient->projectContracts->get(['project_id' => $this->testProjectId]);
        $this->assertIsArray($contracts);
        $this->assertGreaterThan(0, count($contracts));

        // Find our test contract
        $foundContract = null;
        foreach ($contracts as $contract) {
            if ($contract->id === $contractId) {
                $foundContract = $contract;
                break;
            }
        }

        $this->assertNotNull($foundContract);
        $this->assertInstanceOf(ProjectContract::class, $foundContract);
        $this->assertEquals($contractId, $foundContract->id);
        $this->assertEquals($this->testUserId, $foundContract->user_id);

        return $contractId;
    }

    /**
     * @depends testGetAll
     */
    public function testUpdate(int $contractId): int
    {
        $updateData = [
            'project_id' => $this->testProjectId,
            'budget' => 20000.0,
            'hourly_rate' => 150.0,
            'billable' => false
        ];

        $updatedContract = $this->mocoClient->projectContracts->update($contractId, $updateData);
        $this->assertInstanceOf(ProjectContract::class, $updatedContract);
        $this->assertEquals($contractId, $updatedContract->id);
        $this->assertEquals(20000.0, $updatedContract->budget);
        $this->assertEquals(150.0, $updatedContract->hourly_rate);
        $this->assertFalse($updatedContract->billable);
        $this->assertEquals($this->testUserId, $updatedContract->user_id);

        return $contractId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $contractId): void
    {
        $this->mocoClient->projectContracts->delete($this->testProjectId, $contractId);

        // Verify the contract was deleted by trying to get it
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->projectContracts->get(['project_id' => $this->testProjectId, 'id' => $contractId]);
    }

    public function testCreateMinimal(): void
    {
        $createData = [
            'project_id' => $this->testProjectId,
            'user_id' => $this->testUserId
        ];

        try {
            $contract = $this->mocoClient->projectContracts->create($createData);
            $this->assertInstanceOf(ProjectContract::class, $contract);
            $this->assertNotNull($contract->id);
            $this->assertEquals($this->testUserId, $contract->user_id);
            $this->assertTrue($contract->active); // Should default to true

            // Clean up
            $this->mocoClient->projectContracts->delete($this->testProjectId, $contract->id);
        } catch (\Exception $e) {
            // Skip this test if user is already assigned
            if (strpos($e->getMessage(), 'already assigned') !== false) {
                $this->markTestSkipped('User already assigned to project: ' . $e->getMessage());
            } else {
                throw $e;
            }
        }
    }
}
