<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use Moco\Entity\PlanningEntry;
use Moco\Exception\NotFoundException;

class PlanningEntriesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "comment" => "Meeting with client",
        "user" => [
            "id" => 933590696,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "project" => [
            "id" => 944514545,
            "name" => "Website Redesign"
        ],
        "deal" => null,
        "starts_on" => "2024-01-15",
        "ends_on" => "2024-01-19",
        "hours_per_day" => 4.0,
        "symbol" => 2,
        "tentative" => false,
        "created_at" => "2024-01-10T09:00:00Z",
        "updated_at" => "2024-01-10T09:00:00Z"
    ];

    public function testGetAll(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $entries = $this->mocoClient->planningEntries->get([]);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(PlanningEntry::class, $entries[0]);
        $this->assertEquals(12345, $entries[0]->id);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $entry = $this->mocoClient->planningEntries->get(12345);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertEquals(12345, $entry->id);
        $this->assertEquals("Meeting with client", $entry->comment);
        $this->assertEquals("2024-01-15", $entry->starts_on);
        $this->assertEquals("2024-01-19", $entry->ends_on);
        $this->assertEquals(4.0, $entry->hours_per_day);
        $this->assertEquals(2, $entry->symbol);
        $this->assertFalse($entry->tentative);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->planningEntries->get(999999);
    }

    public function testCreate(): void
    {
        $createData = [
            'project_id' => 944514545,
            'starts_on' => '2024-01-15',
            'ends_on' => '2024-01-19',
            'hours_per_day' => 4.0,
            'comment' => 'Meeting with client',
            'symbol' => 2
        ];

        $this->mockResponse(201, json_encode($this->expectedResponse));
        $entry = $this->mocoClient->planningEntries->create($createData);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertEquals(12345, $entry->id);
        $this->assertEquals("Meeting with client", $entry->comment);
        $this->assertEquals(4.0, $entry->hours_per_day);
    }

    public function testUpdate(): void
    {
        $updateData = [
            'hours_per_day' => 6.0,
            'comment' => 'Extended meeting with client'
        ];

        $updatedResponse = $this->expectedResponse;
        $updatedResponse['hours_per_day'] = 6.0;
        $updatedResponse['comment'] = 'Extended meeting with client';

        $this->mockResponse(200, json_encode($updatedResponse));
        $entry = $this->mocoClient->planningEntries->update(12345, $updateData);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertEquals(12345, $entry->id);
        $this->assertEquals(6.0, $entry->hours_per_day);
        $this->assertEquals("Extended meeting with client", $entry->comment);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $result = $this->mocoClient->planningEntries->delete(12345);
        $this->assertNull($result);
    }

    public function testCreateWithDeal(): void
    {
        $createData = [
            'deal_id' => 12345,
            'starts_on' => '2024-01-15',
            'ends_on' => '2024-01-19',
            'hours_per_day' => 3.0
        ];

        $dealResponse = $this->expectedResponse;
        $dealResponse['project'] = null;
        $dealResponse['deal'] = ['id' => 12345, 'name' => 'New Deal'];
        $dealResponse['hours_per_day'] = 3.0;

        $this->mockResponse(201, json_encode($dealResponse));
        $entry = $this->mocoClient->planningEntries->create($createData);
        $this->assertInstanceOf(PlanningEntry::class, $entry);
        $this->assertEquals(3.0, $entry->hours_per_day);
        $this->assertNull($entry->project);
        $this->assertNotNull($entry->deal);
    }

    public function testGetAllWithFilters(): void
    {
        $filters = [
            'user_id' => 933590696,
            'project_id' => 944514545,
            'period' => '2024-01'
        ];

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $entries = $this->mocoClient->planningEntries->get($filters);
        $this->assertIsArray($entries);
        $this->assertCount(1, $entries);
        $this->assertInstanceOf(PlanningEntry::class, $entries[0]);
    }
}
