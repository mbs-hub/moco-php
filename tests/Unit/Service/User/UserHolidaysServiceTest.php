<?php

namespace Tests\Unit\Service\User;

use Moco\Entity\UserHoliday;
use Tests\Unit\Service\AbstractServiceTest;

class UserHolidaysServiceTest extends AbstractServiceTest
{
    private array $expectedHoliday = [
        'id' => 12345,
        'year' => 2019,
        'title' => 'Annual Leave Allowance',
        'days' => 25.0,
        'hours' => 200.0,
        'user' => [
            'id' => 933590696,
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        'created_at' => '2018-10-17T09:33:46Z',
        'updated_at' => '2018-10-17T09:33:46Z'
    ];

    public function testGetSingle(): void
    {
        $this->mockResponse(200, json_encode($this->expectedHoliday));
        $holiday = $this->mocoClient->userHolidays->get(12345);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($this->expectedHoliday['id'], $holiday->id);
        $this->assertEquals($this->expectedHoliday['year'], $holiday->year);
        $this->assertEquals($this->expectedHoliday['title'], $holiday->title);
        $this->assertEquals($this->expectedHoliday['days'], $holiday->days);
        $this->assertEquals($this->expectedHoliday['hours'], $holiday->hours);
        $this->assertTrue(is_array($holiday->user) || is_object($holiday->user));
    }

    public function testGetList(): void
    {
        $expectedHolidays = [$this->expectedHoliday];
        $this->mockResponse(200, json_encode($expectedHolidays));
        $holidays = $this->mocoClient->userHolidays->get([]);

        $this->assertIsArray($holidays);
        $this->assertCount(1, $holidays);
        $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
        $this->assertEquals($this->expectedHoliday['id'], $holidays[0]->id);
    }

    public function testGetListWithYearFilter(): void
    {
        $expectedHolidays = [$this->expectedHoliday];
        $this->mockResponse(200, json_encode($expectedHolidays));
        $holidays = $this->mocoClient->userHolidays->get(['year' => 2019]);

        $this->assertIsArray($holidays);
        $this->assertCount(1, $holidays);
        $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
    }

    public function testGetListWithUserIdFilter(): void
    {
        $expectedHolidays = [$this->expectedHoliday];
        $this->mockResponse(200, json_encode($expectedHolidays));
        $holidays = $this->mocoClient->userHolidays->get(['user_id' => 933590696]);

        $this->assertIsArray($holidays);
        $this->assertCount(1, $holidays);
        $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
    }

    public function testGetListWithBothFilters(): void
    {
        $expectedHolidays = [$this->expectedHoliday];
        $this->mockResponse(200, json_encode($expectedHolidays));
        $holidays = $this->mocoClient->userHolidays->get([
            'year' => 2019,
            'user_id' => 933590696
        ]);

        $this->assertIsArray($holidays);
        $this->assertCount(1, $holidays);
        $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
    }

    public function testGetEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $holidays = $this->mocoClient->userHolidays->get([]);

        $this->assertIsArray($holidays);
        $this->assertEmpty($holidays);
    }

    public function testCreate(): void
    {
        $params = [
            'year' => 2020,
            'title' => 'Vacation Days',
            'days' => 30,
            'user_id' => 933590696
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedHoliday, $params)));
        $holiday = $this->mocoClient->userHolidays->create($params);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($params['year'], $holiday->year);
        $this->assertEquals($params['title'], $holiday->title);
        $this->assertIsNumeric($holiday->days);
    }

    public function testCreateWithAllFields(): void
    {
        $params = [
            'year' => 2021,
            'title' => 'Personal Leave',
            'days' => 15,
            'user_id' => 933590696,
            'creator_id' => 123456
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedHoliday, $params)));
        $holiday = $this->mocoClient->userHolidays->create($params);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($params['year'], $holiday->year);
        $this->assertEquals($params['title'], $holiday->title);
        $this->assertIsNumeric($holiday->days);
    }

    public function testUpdate(): void
    {
        $holidayId = 12345;
        $params = [
            'title' => 'Updated Leave Title',
            'days' => 28
        ];
        $updatedHoliday = array_merge($this->expectedHoliday, $params);

        $this->mockResponse(200, json_encode($updatedHoliday));
        $holiday = $this->mocoClient->userHolidays->update($holidayId, $params);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($params['title'], $holiday->title);
        $this->assertIsNumeric($holiday->days);
        $this->assertEquals($holidayId, $holiday->id);
    }

    public function testUpdateYear(): void
    {
        $holidayId = 12345;
        $params = ['year' => 2022];
        $updatedHoliday = array_merge($this->expectedHoliday, $params);

        $this->mockResponse(200, json_encode($updatedHoliday));
        $holiday = $this->mocoClient->userHolidays->update($holidayId, $params);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($params['year'], $holiday->year);
    }

    public function testDelete(): void
    {
        $holidayId = 12345;
        $this->mockResponse(204, '');
        $result = $this->mocoClient->userHolidays->delete($holidayId);

        $this->assertNull($result);
    }

    public function testEntityAllProperties(): void
    {
        $this->mockResponse(200, json_encode($this->expectedHoliday));
        $holiday = $this->mocoClient->userHolidays->get(12345);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertIsInt($holiday->id);
        $this->assertIsInt($holiday->year);
        $this->assertIsString($holiday->title);
        $this->assertIsNumeric($holiday->days);
        $this->assertIsNumeric($holiday->hours);
        $this->assertTrue(is_array($holiday->user) || is_object($holiday->user));
        $this->assertIsString($holiday->created_at);
        $this->assertIsString($holiday->updated_at);
    }

    public function testUserStructure(): void
    {
        $this->mockResponse(200, json_encode($this->expectedHoliday));
        $holiday = $this->mocoClient->userHolidays->get(12345);

        $userArray = (array) $holiday->user;
        $this->assertArrayHasKey('id', $userArray);
        $this->assertArrayHasKey('firstname', $userArray);
        $this->assertArrayHasKey('lastname', $userArray);
        $this->assertIsInt($userArray['id']);
        $this->assertIsString($userArray['firstname']);
        $this->assertIsString($userArray['lastname']);
    }

    public function testDaysHoursRelationship(): void
    {
        $this->mockResponse(200, json_encode($this->expectedHoliday));
        $holiday = $this->mocoClient->userHolidays->get(12345);

        $this->assertIsNumeric($holiday->days);
        $this->assertIsNumeric($holiday->hours);
        // API automatically calculates hours from days
        $this->assertGreaterThan(0, $holiday->days);
        $this->assertGreaterThan(0, $holiday->hours);
    }

    public function testEntityMandatoryFields(): void
    {
        $holiday = new UserHoliday();
        $mandatoryFields = $holiday->getMandatoryFields();

        $this->assertEquals(['year', 'title', 'days', 'user_id'], $mandatoryFields);
    }

    public function testServiceEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->mocoClient->userHolidays);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        $endpoint = $method->invoke($this->mocoClient->userHolidays);

        $this->assertStringContainsString('users/holidays', $endpoint);
    }

    public function testGetWithNullParameter(): void
    {
        $expectedHolidays = [$this->expectedHoliday];
        $this->mockResponse(200, json_encode($expectedHolidays));
        $holidays = $this->mocoClient->userHolidays->get();

        $this->assertIsArray($holidays);
        $this->assertCount(1, $holidays);
        $this->assertInstanceOf(UserHoliday::class, $holidays[0]);
    }

    public function testCreateMinimalFields(): void
    {
        $params = [
            'year' => 2020,
            'title' => 'Basic Holiday',
            'days' => 20,
            'user_id' => 933590696
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedHoliday, $params)));
        $holiday = $this->mocoClient->userHolidays->create($params);

        $this->assertInstanceOf(UserHoliday::class, $holiday);
        $this->assertEquals($params['year'], $holiday->year);
        $this->assertEquals($params['title'], $holiday->title);
    }
}
