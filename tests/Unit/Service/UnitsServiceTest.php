<?php

namespace Tests\Unit\Service;

use Moco\Entity\Unit;

class UnitsServiceTest extends AbstractServiceTest
{
    private array $expectedUnit = [
        'id' => 909147861,
        'name' => 'C Office',
        'users' => [
            [
                'id' => 933590158,
                'firstname' => 'Tobias',
                'lastname' => 'Miesel',
                'email' => 'tobias@domain.com'
            ]
        ],
        'created_at' => '2018-10-17T09:33:46Z',
        'updated_at' => '2018-10-17T09:33:46Z'
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedUnit));
        $unit = $this->mocoClient->units->get(909147861);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals($this->expectedUnit['id'], $unit->id);
        $this->assertEquals($this->expectedUnit['name'], $unit->name);
        $this->assertIsArray($unit->users);
    }

    public function testGetAll(): void
    {
        $expectedUnits = [$this->expectedUnit];
        $this->mockResponse(200, json_encode($expectedUnits));
        $units = $this->mocoClient->units->get();

        $this->assertIsArray($units);
        $this->assertCount(1, $units);
        $this->assertInstanceOf(Unit::class, $units[0]);
        $this->assertEquals($this->expectedUnit['name'], $units[0]->name);
    }

    public function testGetWithFilters(): void
    {
        $filters = ['name' => 'C Office'];
        $this->mockResponse(200, json_encode([$this->expectedUnit]));
        $units = $this->mocoClient->units->get($filters);

        $this->assertIsArray($units);
        $this->assertCount(1, $units);
        $this->assertInstanceOf(Unit::class, $units[0]);
    }

    public function testGetEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $units = $this->mocoClient->units->get();

        $this->assertIsArray($units);
        $this->assertEmpty($units);
    }

    public function testCreate(): void
    {
        $params = ['name' => 'New Team'];
        $this->mockResponse(200, json_encode(array_merge($this->expectedUnit, $params)));
        $unit = $this->mocoClient->units->create($params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('New Team', $unit->name);
    }

    public function testCreateWithMandatoryFields(): void
    {
        $params = ['name' => 'Required Team'];
        $this->mockResponse(200, json_encode(array_merge($this->expectedUnit, $params)));
        $unit = $this->mocoClient->units->create($params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Required Team', $unit->name);
    }

    public function testUpdate(): void
    {
        $unitId = 909147861;
        $params = ['name' => 'Updated Team Name'];
        $updatedUnit = array_merge($this->expectedUnit, $params);

        $this->mockResponse(200, json_encode($updatedUnit));
        $unit = $this->mocoClient->units->update($unitId, $params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Updated Team Name', $unit->name);
        $this->assertEquals($unitId, $unit->id);
    }

    public function testUpdatePartial(): void
    {
        $unitId = 909147861;
        $params = ['name' => 'Partially Updated'];
        $updatedUnit = array_merge($this->expectedUnit, $params);

        $this->mockResponse(200, json_encode($updatedUnit));
        $unit = $this->mocoClient->units->update($unitId, $params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Partially Updated', $unit->name);
    }

    public function testDelete(): void
    {
        $unitId = 909147861;
        $this->mockResponse(204, '');
        $result = $this->mocoClient->units->delete($unitId);

        $this->assertNull($result);
    }

    public function testDeleteWithUsers(): void
    {
        // This would typically fail in real API if users are assigned
        $unitId = 909147861;
        $this->mockResponse(204, '');
        $result = $this->mocoClient->units->delete($unitId);

        $this->assertNull($result);
    }

    public function testCreateWithEmptyUsers(): void
    {
        $params = ['name' => 'Empty Team'];
        $unitData = array_merge($this->expectedUnit, $params, ['users' => []]);

        $this->mockResponse(200, json_encode($unitData));
        $unit = $this->mocoClient->units->create($params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Empty Team', $unit->name);
        $this->assertEquals([], $unit->users);
    }

    public function testCreateWithMultipleUsers(): void
    {
        $params = ['name' => 'Multi User Team'];
        $multipleUsers = [
            [
                'id' => 933590158,
                'firstname' => 'Tobias',
                'lastname' => 'Miesel',
                'email' => 'tobias@domain.com'
            ],
            [
                'id' => 933590159,
                'firstname' => 'John',
                'lastname' => 'Doe',
                'email' => 'john@domain.com'
            ]
        ];
        $unitData = array_merge($this->expectedUnit, $params, ['users' => $multipleUsers]);

        $this->mockResponse(200, json_encode($unitData));
        $unit = $this->mocoClient->units->create($params);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Multi User Team', $unit->name);
        $this->assertCount(2, $unit->users);
    }

    public function testGetSingleUnitProperties(): void
    {
        $this->mockResponse(200, json_encode($this->expectedUnit));
        $unit = $this->mocoClient->units->get(909147861);

        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertIsInt($unit->id);
        $this->assertIsString($unit->name);
        $this->assertIsArray($unit->users);
        $this->assertIsString($unit->created_at);
        $this->assertIsString($unit->updated_at);
    }

    public function testEntityMandatoryFields(): void
    {
        $unit = new Unit();
        $mandatoryFields = $unit->getMandatoryFields();

        $this->assertEquals(['name'], $mandatoryFields);
    }

    public function testServiceEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->mocoClient->units);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        $endpoint = $method->invoke($this->mocoClient->units);

        $this->assertStringContainsString('units', $endpoint);
    }
}
