<?php

namespace Functional\Service;

use Moco\Entity\Unit;
use Tests\Functional\Service\AbstractServiceTest;

class UnitsServiceTest extends AbstractServiceTest
{
    private int $testUnitId = 909195187; // Use an existing test unit ID

    public function testGetAllUnits(): void
    {
        $units = $this->mocoClient->units->get();

        if (!empty($units)) {
            $this->assertInstanceOf(Unit::class, $units[0]);
            $this->assertIsInt($units[0]->id);
            $this->assertIsString($units[0]->name);
        }
    }

    public function testGetSingleUnit(): void
    {
        $unit = $this->mocoClient->units->get($this->testUnitId);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals($this->testUnitId, $unit->id);
        $this->assertIsString($unit->name);
        $this->assertIsArray($unit->users);
        $this->assertIsString($unit->created_at);
        $this->assertIsString($unit->updated_at);
    }

    public function testCreateUnit(): void
    {
        $unitData = [
            'name' => 'Test Unit ' . time()
        ];

        $unit = $this->mocoClient->units->create($unitData);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals($unitData['name'], $unit->name);
        $this->assertIsInt($unit->id);
        $this->assertIsArray($unit->users);
        $this->assertEmpty($unit->users); // New units start with no users

        // Store the ID for cleanup
        $createdUnitId = $unit->id;

        // Clean up - delete the created unit
        $this->mocoClient->units->delete($createdUnitId);
    }

    public function testUpdateUnit(): void
    {
        // First create a unit to update
        $unitData = [
            'name' => 'Unit for Update Test ' . time()
        ];
        $unit = $this->mocoClient->units->create($unitData);
        $unitId = $unit->id;

        // Update the unit
        $updateData = [
            'name' => 'Updated Unit Name ' . time()
        ];
        $updatedUnit = $this->mocoClient->units->update($unitId, $updateData);

        $this->assertInstanceOf(Unit::class, $updatedUnit);
        $this->assertEquals($updateData['name'], $updatedUnit->name);
        $this->assertEquals($unitId, $updatedUnit->id);

        // Clean up
        $this->mocoClient->units->delete($unitId);
    }

    public function testDeleteEmptyUnit(): void
    {
        // Create a unit with no users assigned
        $unitData = [
            'name' => 'Unit for Delete Test ' . time()
        ];
        $unit = $this->mocoClient->units->create($unitData);
        $unitId = $unit->id;

        // Delete the unit (should succeed since no users are assigned)
        $result = $this->mocoClient->units->delete($unitId);

        $this->assertNull($result);

        // Verify the unit was deleted by trying to get it (should throw exception)
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->units->get($unitId);
    }

    public function testGetUnitsWithFilters(): void
    {
        // Test filtering by name (if supported)
        $allUnits = $this->mocoClient->units->get();

        if (!empty($allUnits)) {
            $firstUnitName = $allUnits[0]->name;
            $filteredUnits = $this->mocoClient->units->get(['name' => $firstUnitName]);

            $this->assertIsArray($filteredUnits);

            foreach ($filteredUnits as $unit) {
                $this->assertInstanceOf(Unit::class, $unit);
                $this->assertStringContainsString($firstUnitName, $unit->name);
            }
        }
    }

    public function testCompleteWorkflow(): void
    {
        $unitName = 'Workflow Test Unit ' . time();

        // 1. Create a new unit
        $createData = ['name' => $unitName];
        $createdUnit = $this->mocoClient->units->create($createData);

        $this->assertInstanceOf(Unit::class, $createdUnit);
        $this->assertEquals($unitName, $createdUnit->name);
        $unitId = $createdUnit->id;

        // 2. Get the created unit
        $fetchedUnit = $this->mocoClient->units->get($unitId);
        $this->assertEquals($unitId, $fetchedUnit->id);
        $this->assertEquals($unitName, $fetchedUnit->name);

        // 3. Update the unit
        $updatedName = 'Updated ' . $unitName;
        $updatedUnit = $this->mocoClient->units->update($unitId, ['name' => $updatedName]);
        $this->assertEquals($updatedName, $updatedUnit->name);

        // 4. Verify the update
        $finalUnit = $this->mocoClient->units->get($unitId);
        $this->assertEquals($updatedName, $finalUnit->name);

        // 5. Delete the unit
        $this->mocoClient->units->delete($unitId);

        // 6. Verify deletion (should throw NotFoundException)
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->units->get($unitId);
    }
}
