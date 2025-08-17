<?php

namespace Tests\Unit\Service;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;

class SchedulesServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123,
        "date" => "2023-12-01",
        "comment" => "Annual vacation",
        "am" => true,
        "pm" => true,
        "assignment" => [
            "id" => 789,
            "name" => "Assignment details"
        ],
        "user" => [
            "id" => 456,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "absence_code" => 4,
        "symbol" => "🏖️",
        "created_at" => "2023-12-01T10:00:00Z",
        "updated_at" => "2023-12-01T10:00:00Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "date" => "2023-12-01",
            "absence_code" => 4,
            "comment" => "Annual vacation",
            "am" => true,
            "pm" => true
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $schedule = $this->mocoClient->schedules->create($params);
        $this->assertEquals(123, $schedule->id);
        $this->assertEquals("2023-12-01", $schedule->date);
        $this->assertEquals(4, $schedule->absence_code);
        $this->assertTrue($schedule->am);
        $this->assertTrue($schedule->pm);

        // Test missing mandatory field
        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->schedules->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $schedules = $this->mocoClient->schedules->get();
        $this->assertEquals(123, $schedules[0]->id);
        $this->assertEquals("Annual vacation", $schedules[0]->comment);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $schedule = $this->mocoClient->schedules->get(123);
        $this->assertEquals("2023-12-01", $schedule->date);
        $this->assertEquals(4, $schedule->absence_code);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->schedules->get(999);
    }

    public function testGetWithParams(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $schedules = $this->mocoClient->schedules->get(['user_id' => 456]);
        $this->assertEquals(123, $schedules[0]->id);

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $schedules = $this->mocoClient->schedules->get([
            'from' => '2023-12-01',
            'to' => '2023-12-31'
        ]);
        $this->assertEquals(123, $schedules[0]->id);

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $schedules = $this->mocoClient->schedules->get(['absence_code' => 4]);
        $this->assertEquals(4, $schedules[0]->absence_code);
    }

    public function testUpdate(): void
    {
        $this->expectedResponse['comment'] = 'Updated vacation comment';
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $schedule = $this->mocoClient->schedules->update(123, ['comment' => 'Updated vacation comment']);
        $this->assertEquals('Updated vacation comment', $schedule->comment);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->schedules->delete(123));
    }

    public function testCreateWithDifferentAbsenceCodes(): void
    {
        $absenceCodes = [
            1 => 'Unplannable absence',
            2 => 'Public holiday',
            3 => 'Sick day',
            4 => 'Holiday',
            5 => 'Absence'
        ];

        foreach ($absenceCodes as $code => $description) {
            $params = [
                "date" => "2023-12-01",
                "absence_code" => $code,
                "comment" => $description
            ];

            $expectedResponse = $this->expectedResponse;
            $expectedResponse['absence_code'] = $code;
            $expectedResponse['comment'] = $description;

            $this->mockResponse(200, json_encode($expectedResponse));
            $schedule = $this->mocoClient->schedules->create($params);
            $this->assertEquals($code, $schedule->absence_code);
            $this->assertEquals($description, $schedule->comment);
        }
    }

    public function testCreateWithTimeVariations(): void
    {
        // Test morning only
        $morningOnly = [
            "date" => "2023-12-01",
            "absence_code" => 4,
            "am" => true,
            "pm" => false
        ];

        $expectedMorning = $this->expectedResponse;
        $expectedMorning['am'] = true;
        $expectedMorning['pm'] = false;

        $this->mockResponse(200, json_encode($expectedMorning));
        $schedule = $this->mocoClient->schedules->create($morningOnly);
        $this->assertTrue($schedule->am);
        $this->assertFalse($schedule->pm);

        // Test afternoon only
        $afternoonOnly = [
            "date" => "2023-12-01",
            "absence_code" => 4,
            "am" => false,
            "pm" => true
        ];

        $expectedAfternoon = $this->expectedResponse;
        $expectedAfternoon['am'] = false;
        $expectedAfternoon['pm'] = true;

        $this->mockResponse(200, json_encode($expectedAfternoon));
        $schedule = $this->mocoClient->schedules->create($afternoonOnly);
        $this->assertFalse($schedule->am);
        $this->assertTrue($schedule->pm);
    }

    public function testMandatoryFieldValidation(): void
    {
        // Test missing absence_code
        $paramsWithoutAbsenceCode = [
            "date" => "2023-12-01",
            "comment" => "Test"
        ];

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->schedules->create($paramsWithoutAbsenceCode);
    }
}
