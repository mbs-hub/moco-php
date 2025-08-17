<?php

namespace Tests\Unit\Service\Projects;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id"                          => 1234567,
        "identifier"                  => "P001",
        "name"                        => "Moco-PHP",
        "active"                      => true,
        "billable"                    => true,
        "fixed_price"                 => true,
        "retainer"                    => false,
        "start_date"                  => null,
        "finish_date"                 => "2018-12-31",
        "color"                       => "#CCCC00",
        "currency"                    => "EUR",
        "billing_variant"             => "project",
        "billing_address"             => "Beispiel AG\nHerr Maier\nBeispielstrasse...",
        "billing_email_to"            => "project@beispiel.co",
        "billing_email_cc"            => "project-cc@beispiel.co",
        "billing_notes"               => "Billig notes text",
        "setting_include_time_report" => true,
        "budget"                      => 18200,
        "budget_monthly"              => null,
        "budget_expenses"             => 8200,
        "hourly_rate"                 => 150,
        "info"                        => "Abrechnung jährlich",
        "tags"                        => ["Print", "Digital"],
        "custom_properties"           => [
            "Project Management" => "https://basecamp.com/123456",
        ],
        "leader"                      => [
            "id"        => 123,
            "firstname" => "Michael",
            "lastname"  => "Mustermann",
        ],
        "co_leader"                   => null,
        "customer"                    => [
            "id"   => 123,
            "name" => "Beispiel AG",
        ],
        "deal"                        => [
            "id"   => 5635453,
            "name" => "Website Relaunch",
        ],
        "tasks"                       => [
            [
                "id"          => 125112,
                "name"        => "Project Management",
                "billable"    => true,
                "active"      => true,
                "budget"      => null,
                "hourly_rate" => 0,
            ],
            [
                "id"          => 125111,
                "name"        => "Development",
                "billable"    => true,
                "active"      => true,
                "budget"      => null,
                "hourly_rate" => 0,
            ],
        ],
        "contracts"                   => [
            [
                "id"          => 458639048,
                "user_id"     => 933590696,
                "firstname"   => "Michael",
                "lastname"    => "Mustermann",
                "billable"    => true,
                "active"      => true,
                "budget"      => null,
                "hourly_rate" => 0,
            ],
            [
                "id"          => 458672097,
                "user_id"     => 933736920,
                "firstname"   => "Nicola",
                "lastname"    => "Piccinini",
                "billable"    => true,
                "active"      => true,
                "budget"      => null,
                "hourly_rate" => 0,
            ],
        ],
        "created_at"                  => "2018-10-17T09:33:46Z",
        "updated_at"                  => "2018-10-17T09:33:46Z",
    ];

    public function testCreate(): void
    {
        $params =
            [
                "name"        => "Moco-PHP",
                "currency"    => "EUR",
                "leader_id"   => 123,
                "customer_id" => 123,
                "tags"        => ["Print", "Digital"],
            ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $project = $this->mocoClient->projects->create($params);
        $this->assertEquals(1234567, $project->id);

        unset($params['customer_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->projects->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $projects = $this->mocoClient->projects->get();
        $this->assertEquals(1234567, $projects[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $project = $this->mocoClient->projects->get(1234567);
        $this->assertEquals('Moco-PHP', $project->name);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projects->get(12);
    }

    public function testUpdate(): void
    {
        $this->expectedResponse['name'] = 'name updated';
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $project = $this->mocoClient->projects->update(1234567, $this->expectedResponse);
        $this->assertEquals('name updated', $project->name);
    }

    public function testAssignedProjects(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $result = $this->mocoClient->projects->getAssignedProjects();
        $this->assertIsArray($result);
    }

    public function testArchive(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $result = $this->mocoClient->projects->archive(1234567);
        $this->assertEquals(1234567, $result->id);
    }

    public function testUnarchive(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $project = $this->mocoClient->projects->unarchive(1234567);
        $this->assertEquals(1234567, $project->id);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->projects->delete(1234567));
    }
}
