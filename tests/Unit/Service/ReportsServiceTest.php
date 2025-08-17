<?php

namespace Tests\Unit\Service;

class ReportsServiceTest extends AbstractServiceTest
{
    private array $expectedAbsenceResponse = [
        [
            "user" => [
                "id" => 123,
                "firstname" => "Jane",
                "lastname" => "Doe"
            ],
            "total_vacation_days" => 25.0,
            "used_vacation_days" => 10.5,
            "planned_vacation_days" => 5.0,
            "sickdays" => 4.0
        ],
        [
            "user" => [
                "id" => 456,
                "firstname" => "John",
                "lastname" => "Smith"
            ],
            "total_vacation_days" => 30.0,
            "used_vacation_days" => 15.0,
            "planned_vacation_days" => 8.0,
            "sickdays" => 2.5
        ]
    ];

    public function testGetAbsences(): void
    {
        $this->mockResponse(200, json_encode($this->expectedAbsenceResponse));
        $reports = $this->mocoClient->reports->getAbsences();

        $this->assertIsArray($reports);
        $this->assertCount(2, $reports);

        $this->assertEquals(25.0, $reports[0]->total_vacation_days);
        $this->assertEquals(10.5, $reports[0]->used_vacation_days);
        $this->assertEquals(5.0, $reports[0]->planned_vacation_days);
        $this->assertEquals(4.0, $reports[0]->sickdays);
    }

    public function testGetAbsencesWithActiveFilter(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedAbsenceResponse[0]]));
        $reports = $this->mocoClient->reports->getAbsences(['active' => true]);

        $this->assertIsArray($reports);
        $this->assertCount(1, $reports);
        $this->assertEquals(123, $reports[0]->user->id);
    }

    public function testGetAbsencesWithYearFilter(): void
    {
        $this->mockResponse(200, json_encode($this->expectedAbsenceResponse));
        $reports = $this->mocoClient->reports->getAbsences(['year' => 2023]);

        $this->assertIsArray($reports);
        $this->assertCount(2, $reports);
    }

    public function testGetAbsencesWithMultipleFilters(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedAbsenceResponse[1]]));
        $reports = $this->mocoClient->reports->getAbsences([
            'active' => true,
            'year' => 2023
        ]);

        $this->assertIsArray($reports);
        $this->assertCount(1, $reports);
        $this->assertEquals(456, $reports[0]->user->id);
        $this->assertEquals("John", $reports[0]->user->firstname);
    }

    public function testGetAbsencesEmptyResponse(): void
    {
        $this->mockResponse(200, json_encode([]));
        $reports = $this->mocoClient->reports->getAbsences();

        $this->assertIsArray($reports);
        $this->assertCount(0, $reports);
    }
}
