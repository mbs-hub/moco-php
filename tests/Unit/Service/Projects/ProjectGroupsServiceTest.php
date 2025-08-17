<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectGroup;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectGroupsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 234,
        "name" => "Project Group Example",
        "user" => [
            "id" => 947556942,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "company" => [
            "id" => 760269,
            "name" => "Example Company"
        ],
        "budget" => 42000.0,
        "currency" => "EUR",
        "info" => "This is an example project group",
        "custom_properties" => [],
        "customer_report_url" => "https://example.com/report",
        "projects" => [
            [
                "id" => 947556942,
                "name" => "Project One"
            ],
            [
                "id" => 944514546,
                "name" => "Project Two"
            ]
        ],
        "created_at" => "2024-01-10T09:00:00Z",
        "updated_at" => "2024-01-10T09:00:00Z"
    ];

    public function testGetAll(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $projectGroups = $this->mocoClient->projectGroups->get([]);
        $this->assertIsArray($projectGroups);
        $this->assertCount(1, $projectGroups);
        $this->assertInstanceOf(ProjectGroup::class, $projectGroups[0]);
        $this->assertEquals(234, $projectGroups[0]->id);
        $this->assertEquals("Project Group Example", $projectGroups[0]->name);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $projectGroup = $this->mocoClient->projectGroups->get(234);
        $this->assertInstanceOf(ProjectGroup::class, $projectGroup);
        $this->assertEquals(234, $projectGroup->id);
        $this->assertEquals("Project Group Example", $projectGroup->name);
        $this->assertEquals(947556942, $projectGroup->user->id);
        $this->assertEquals("John", $projectGroup->user->firstname);
        $this->assertEquals("Doe", $projectGroup->user->lastname);
        $this->assertEquals(760269, $projectGroup->company->id);
        $this->assertEquals("Example Company", $projectGroup->company->name);
        $this->assertEquals(42000.0, $projectGroup->budget);
        $this->assertEquals("EUR", $projectGroup->currency);
        $this->assertEquals("This is an example project group", $projectGroup->info);
        $this->assertEquals("https://example.com/report", $projectGroup->customer_report_url);
        $this->assertIsArray($projectGroup->projects);
        $this->assertCount(2, $projectGroup->projects);
        $this->assertEquals(947556942, $projectGroup->projects[0]->id);
        $this->assertEquals("Project One", $projectGroup->projects[0]->name);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectGroups->get(999999);
    }

    public function testGetAllWithFilters(): void
    {
        $filters = [
            'user_id' => 933590696,
            'company_id' => 760269
        ];

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $projectGroups = $this->mocoClient->projectGroups->get($filters);
        $this->assertIsArray($projectGroups);
        $this->assertCount(1, $projectGroups);
        $this->assertInstanceOf(ProjectGroup::class, $projectGroups[0]);
        $this->assertEquals(234, $projectGroups[0]->id);
        $this->assertEquals(947556942, $projectGroups[0]->user->id);
        $this->assertEquals(760269, $projectGroups[0]->company->id);
    }

    public function testGetAllEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $projectGroups = $this->mocoClient->projectGroups->get([]);
        $this->assertIsArray($projectGroups);
        $this->assertCount(0, $projectGroups);
    }

    public function testGetProjectGroupWithMinimalData(): void
    {
        $minimalResponse = [
            "id" => 235,
            "name" => "Minimal Project Group",
            "user" => [
                "id" => 933590696,
                "firstname" => "Jane",
                "lastname" => "Smith"
            ],
            "company" => [
                "id" => 760270,
                "name" => "Minimal Company"
            ],
            "budget" => null,
            "currency" => null,
            "info" => null,
            "custom_properties" => [],
            "customer_report_url" => null,
            "projects" => [],
            "created_at" => "2024-01-15T10:00:00Z",
            "updated_at" => "2024-01-15T10:00:00Z"
        ];

        $this->mockResponse(200, json_encode($minimalResponse));
        $projectGroup = $this->mocoClient->projectGroups->get(235);
        $this->assertInstanceOf(ProjectGroup::class, $projectGroup);
        $this->assertEquals(235, $projectGroup->id);
        $this->assertEquals("Minimal Project Group", $projectGroup->name);
        $this->assertNull($projectGroup->budget);
        $this->assertNull($projectGroup->currency);
        $this->assertNull($projectGroup->info);
        $this->assertNull($projectGroup->customer_report_url);
        $this->assertIsArray($projectGroup->projects);
        $this->assertCount(0, $projectGroup->projects);
    }
}
