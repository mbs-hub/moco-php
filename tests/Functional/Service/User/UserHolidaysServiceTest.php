<?php

namespace Functional\Service\User;

use Moco\Entity\UserHoliday;
use Tests\Functional\Service\AbstractServiceTest;

class UserHolidaysServiceTest extends AbstractServiceTest
{
    private int $testUserId = 933736920; // Use an existing test user ID

    public function testGetHolidaysList(): void
    {
        $holidays = $this->mocoClient->userHolidays->get([]);

        $this->assertIsArray($holidays);

        if (!empty($holidays)) {
            $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
            $this->assertIsInt($holidays[0]->id);
            $this->assertIsInt($holidays[0]->year);
            $this->assertIsString($holidays[0]->title);
            $this->assertIsNumeric($holidays[0]->days);
            $this->assertIsNumeric($holidays[0]->hours);
            $this->assertIsObject($holidays[0]->user);
            $this->assertIsString($holidays[0]->created_at);
            $this->assertIsString($holidays[0]->updated_at);
        }
    }

    public function testGetHolidaysWithYearFilter(): void
    {
        $holidays = $this->mocoClient->userHolidays->get(['year' => 2023]);

        $this->assertIsArray($holidays);

        foreach ($holidays as $holiday) {
            $this->assertInstanceOf(UserHoliday::class, $holiday);
            $this->assertEquals(2023, $holiday->year);
        }
    }

    public function testGetHolidaysWithUserFilter(): void
    {
        $holidays = $this->mocoClient->userHolidays->get(['user_id' => $this->testUserId]);

        $this->assertIsArray($holidays);

        foreach ($holidays as $holiday) {
            $this->assertInstanceOf(UserHoliday::class, $holiday);
            $this->assertEquals($this->testUserId, $holiday->user->id);
        }
    }

    public function testGetSingleHoliday(): void
    {
        // First get a list to find a holiday ID
        $holidays = $this->mocoClient->userHolidays->get([]);

        if (!empty($holidays)) {
            $holidayId = $holidays[0]->id;
            $holiday = $this->mocoClient->userHolidays->get($holidayId);

            $this->assertInstanceOf(UserHoliday::class, $holiday);
            $this->assertEquals($holidayId, $holiday->id);
            $this->assertIsInt($holiday->year);
            $this->assertIsString($holiday->title);
            $this->assertIsNumeric($holiday->days);
            $this->assertIsNumeric($holiday->hours);
        }
    }

    public function testCreateHoliday(): void
    {
        $holidayData = [
            'year' => date('Y'),
            'title' => 'Test Holiday ' . time(),
            'days' => 25,
            'user_id' => $this->testUserId
        ];

        $holiday = $this->mocoClient->userHolidays->create($holidayData);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($holidayData['year'], $holiday->year);
        $this->assertEquals($holidayData['title'], $holiday->title);
        $this->assertEquals($holidayData['days'], $holiday->days);
        $this->assertIsInt($holiday->id);

        // Store for cleanup
        $createdHolidayId = $holiday->id;

        // Clean up - delete the created holiday
        $this->mocoClient->userHolidays->delete($createdHolidayId);
    }

    public function testUpdateHoliday(): void
    {
        // First create a holiday to update
        $holidayData = [
            'year' => date('Y'),
            'title' => 'Holiday for Update Test ' . time(),
            'days' => 20,
            'user_id' => $this->testUserId
        ];
        $holiday = $this->mocoClient->userHolidays->create($holidayData);
        $holidayId = $holiday->id;

        // Update the holiday
        $updateData = [
            'title' => 'Updated Holiday Title ' . time(),
            'days' => 30
        ];
        $updatedHoliday = $this->mocoClient->userHolidays->update($holidayId, $updateData);

        $this->assertInstanceOf(UserHoliday::class, $updatedHoliday);
        $this->assertEquals($updateData['title'], $updatedHoliday->title);
        $this->assertEquals($updateData['days'], $updatedHoliday->days);
        $this->assertEquals($holidayId, $updatedHoliday->id);

        // Clean up
        $this->mocoClient->userHolidays->delete($holidayId);
    }

    public function testDeleteHoliday(): void
    {
        // Create a holiday to delete
        $holidayData = [
            'year' => date('Y'),
            'title' => 'Holiday for Delete Test ' . time(),
            'days' => 15,
            'user_id' => $this->testUserId
        ];
        $holiday = $this->mocoClient->userHolidays->create($holidayData);
        $holidayId = $holiday->id;

        // Delete the holiday
        $result = $this->mocoClient->userHolidays->delete($holidayId);

        $this->assertNull($result);

        // Verify the holiday was deleted by trying to get it (should throw exception)
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->userHolidays->get($holidayId);
    }
}
