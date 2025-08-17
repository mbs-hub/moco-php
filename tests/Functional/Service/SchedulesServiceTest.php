<?php

namespace Functional\Service;

use Moco\Entity\Schedule;
use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class SchedulesServiceTest extends AbstractServiceTest
{
    public function testCreate(): Schedule
    {
        $params = [
            "date" => "2023-12-25", // Christmas day
            "absence_code" => 4, // Holiday
            "comment" => "Christmas vacation",
            "am" => true,
            "pm" => true
        ];

        $schedule = $this->mocoClient->schedules->create($params);
        $this->assertInstanceOf(Schedule::class, $schedule);
        $this->assertEquals($params['date'], $schedule->date);
        $this->assertEquals($params['absence_code'], $schedule->absence_code);
        $this->assertEquals($params['comment'], $schedule->comment);

        return $schedule;
    }

    /**
     * @depends testCreate
     */
    public function testGet(Schedule $schedule): int
    {
        $schedules = $this->mocoClient->schedules->get();
        $this->assertIsArray($schedules);

        $filteredSchedules = $this->mocoClient->schedules->get(['absence_code' => 4]);
        $this->assertIsArray($filteredSchedules);

        $singleSchedule = $this->mocoClient->schedules->get($schedule->id);
        $this->assertEquals($schedule->id, $singleSchedule->id);

        return $schedule->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $scheduleId): int
    {
        $schedule = $this->mocoClient->schedules->update($scheduleId, [
            'comment' => 'Updated Christmas vacation'
        ]);
        $this->assertInstanceOf(Schedule::class, $schedule);
        $this->assertEquals('Updated Christmas vacation', $schedule->comment);

        return $scheduleId;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $scheduleId): void
    {
        $this->assertNull($this->mocoClient->schedules->delete($scheduleId));
    }

    public function testGetWithFilters(): void
    {
        // Test date range filter
        $schedules = $this->mocoClient->schedules->get([
            'from' => '2023-01-01',
            'to' => '2023-12-31'
        ]);
        $this->assertIsArray($schedules);

        foreach ($schedules as $schedule) {
            $this->assertInstanceOf(Schedule::class, $schedule);
        }

        // Test user filter
        $userSchedules = $this->mocoClient->schedules->get(['user_id' => 933736920]);
        $this->assertIsArray($userSchedules);

        // Test absence code filter
        $holidaySchedules = $this->mocoClient->schedules->get(['absence_code' => 4]);
        $this->assertIsArray($holidaySchedules);

        foreach ($holidaySchedules as $schedule) {
            $this->assertInstanceOf(Schedule::class, $schedule);
            if (isset($schedule->absence_code)) {
                $this->assertEquals(4, $schedule->absence_code);
            }
        }
    }

    public function testCreateWithDifferentAbsenceCodes(): void
    {
        $testCases = [
            ['code' => 3, 'name' => 'Sick day', 'comment' => 'Flu symptoms'],
            ['code' => 4, 'name' => 'Holiday', 'comment' => 'Summer vacation'],
            ['code' => 5, 'name' => 'Absence', 'comment' => 'Personal matters']
        ];

        $createdSchedules = [];

        foreach ($testCases as $testCase) {
            $params = [
                "date" => "2023-06-" . sprintf("%02d", 10 + $testCase['code']),
                "absence_code" => $testCase['code'],
                "comment" => $testCase['comment'],
                "am" => true,
                "pm" => true
            ];

            $schedule = $this->mocoClient->schedules->create($params);
            $this->assertInstanceOf(Schedule::class, $schedule);
            $this->assertEquals($testCase['code'], $schedule->absence_code);
            $this->assertEquals($testCase['comment'], $schedule->comment);

            $createdSchedules[] = $schedule;
        }

        // Clean up created schedules
        foreach ($createdSchedules as $schedule) {
            try {
                $this->mocoClient->schedules->delete($schedule->id);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }

    public function testCreateWithTimeVariations(): void
    {
        $timeVariations = [
            ['am' => true, 'pm' => false, 'description' => 'Morning only'],
            ['am' => false, 'pm' => true, 'description' => 'Afternoon only'],
            ['am' => true, 'pm' => true, 'description' => 'Full day']
        ];

        $createdSchedules = [];

        foreach ($timeVariations as $index => $variation) {
            $params = [
                "date" => "2023-07-" . sprintf("%02d", 10 + $index),
                "absence_code" => 4,
                "comment" => $variation['description'],
                "am" => $variation['am'],
                "pm" => $variation['pm']
            ];

            $schedule = $this->mocoClient->schedules->create($params);
            $this->assertInstanceOf(Schedule::class, $schedule);
            $this->assertEquals($variation['am'], $schedule->am);
            $this->assertEquals($variation['pm'], $schedule->pm);

            $createdSchedules[] = $schedule;
        }

        // Clean up created schedules
        foreach ($createdSchedules as $schedule) {
            try {
                $this->mocoClient->schedules->delete($schedule->id);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
    }

    public function testValidationErrors(): void
    {
        // Test missing required fields
        $invalidParams = [
            "comment" => "Missing date and absence_code"
        ];

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->schedules->create($invalidParams);
    }

    public function testCreateWithMinimalFields(): void
    {
        $minimalParams = [
            "date" => "2023-08-15",
            "absence_code" => 4
        ];

        $schedule = $this->mocoClient->schedules->create($minimalParams);
        $this->assertInstanceOf(Schedule::class, $schedule);
        $this->assertEquals($minimalParams['date'], $schedule->date);
        $this->assertEquals($minimalParams['absence_code'], $schedule->absence_code);

        // Clean up
        try {
            $this->mocoClient->schedules->delete($schedule->id);
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }
}
