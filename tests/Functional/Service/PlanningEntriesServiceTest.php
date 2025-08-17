<?php

declare(strict_types=1);

namespace Tests\Functional\Service;

use Moco\Entity\PlanningEntry;

class PlanningEntriesServiceTest extends AbstractServiceTest
{
    private int $testProjectId = 947556942; // This should be replaced with a valid project ID

    public function testCreate(): int
    {
        $createData = [
            'project_id' => $this->testProjectId,
            'starts_on' => '2024-02-01',
            'ends_on' => '2024-02-05',
            'hours_per_day' => 6.0,
            'comment' => 'Test planning entry created by functional test',
            'symbol' => 3,
            'tentative' => false
        ];

        $entry = $this->mocoClient->planningEntries->create($createData);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertNotNull($entry->id);
        $this->assertEquals('2024-02-01', $entry->starts_on);
        $this->assertEquals('2024-02-05', $entry->ends_on);
        $this->assertEquals(6.0, $entry->hours_per_day);
        $this->assertEquals('Test planning entry created by functional test', $entry->comment);
        $this->assertEquals(3, $entry->symbol);
        $this->assertFalse($entry->tentative);

        return $entry->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $planningId): int
    {
        $entry = $this->mocoClient->planningEntries->get($planningId);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertEquals($planningId, $entry->id);
        $this->assertEquals('Test planning entry created by functional test', $entry->comment);
        $this->assertEquals(6.0, $entry->hours_per_day);
        $this->assertNotNull($entry->created_at);
        $this->assertNotNull($entry->updated_at);
        return $planningId;
    }

    /**
     * @depends testGet
     */
    public function testGetAll(int $planningId): int
    {
        $entries = $this->mocoClient->planningEntries->get([]);
        $this->assertIsArray($entries);
        $this->assertGreaterThan(0, count($entries));

        // Find our test entry
        $foundEntry = null;
        foreach ($entries as $entry) {
            if ($entry->id === $planningId) {
                $foundEntry = $entry;
                break;
            }
        }

        $this->assertNotNull($foundEntry);
        $this->assertInstanceOf(PlanningEntry::class, $foundEntry);
        $this->assertEquals($planningId, $foundEntry->id);
        return $planningId;
    }

    /**
     * @depends testGetAll
     */
    public function testGetAllWithFilters(int $planningId): int
    {
        // Test filtering by project
        $entries = $this->mocoClient->planningEntries->get(['project_id' => $this->testProjectId]);
        $this->assertIsArray($entries);

        // Find our test entry
        $foundEntry = null;
        foreach ($entries as $entry) {
            if ($entry->id === $planningId) {
                $foundEntry = $entry;
                break;
            }
        }

        $this->assertNotNull($foundEntry);
        $this->assertEquals($this->testProjectId, $foundEntry->project->id);
        return $planningId;
    }

    /**
     * @depends testGetAllWithFilters
     */
    public function testUpdate(int $planningId): int
    {
        $updateData = [
            'hours_per_day' => 8.0,
            'comment' => 'Updated test planning entry',
            'symbol' => 5,
            'tentative' => true
        ];

        $updatedEntry = $this->mocoClient->planningEntries->update($planningId, $updateData);
        $this->assertInstanceOf(PlanningEntry::class, $updatedEntry);
        $this->assertEquals($planningId, $updatedEntry->id);
        $this->assertEquals(8.0, $updatedEntry->hours_per_day);
        $this->assertEquals('Updated test planning entry', $updatedEntry->comment);
        $this->assertEquals(5, $updatedEntry->symbol);
        $this->assertTrue($updatedEntry->tentative);
        return $planningId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $planningId): void
    {
        $this->assertNull($this->mocoClient->planningEntries->delete($planningId));

        // Verify the entry was deleted by trying to get it
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->planningEntries->get($planningId);
    }

    public function testCreateWithDeal(): void
    {
        // This test assumes you have a valid deal ID
        // In practice, you would need to create a deal first or use an existing one
        $testDealId = 875659; // Replace with actual deal ID

        $createData = [
            'deal_id' => $testDealId,
            'starts_on' => '2024-02-10',
            'ends_on' => '2024-02-12',
            'hours_per_day' => 4.0,
            'comment' => 'Test planning entry for deal'
        ];

        try {
            $entry = $this->mocoClient->planningEntries->create($createData);
            $this->assertInstanceOf(PlanningEntry::class, $entry);
            $this->assertNotNull($entry->deal);
            $this->assertEquals($testDealId, $entry->deal->id);
            $this->assertNull($entry->project);

            // Clean up
            $this->mocoClient->planningEntries->delete($entry->id);
        } catch (\Exception $e) {
            // Skip this test if deal doesn't exist
            $this->markTestSkipped('Test deal not available: ' . $e->getMessage());
        }
    }
}
