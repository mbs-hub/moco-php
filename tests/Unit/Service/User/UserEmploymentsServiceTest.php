<?php

namespace Tests\Unit\Service\User;

use Moco\Entity\UserEmployment;
use Tests\Unit\Service\AbstractServiceTest;

class UserEmploymentsServiceTest extends AbstractServiceTest
{
    private array $expectedEmployment = [
        'id' => 123456,
        'weekly_target_hours' => 40.0,
        'pattern' => [
            'am' => [4, 4, 4, 4, 4],
            'pm' => [4, 4, 4, 4, 4]
        ],
        'from' => '2018-01-01',
        'to' => '2018-12-31',
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
        $this->mockResponse(200, json_encode($this->expectedEmployment));
        $employment = $this->mocoClient->userEmployments->get(123456);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertEquals($this->expectedEmployment['id'], $employment->id);
        $this->assertEquals($this->expectedEmployment['weekly_target_hours'], $employment->weekly_target_hours);
        $this->assertTrue(is_array($employment->pattern) || is_object($employment->pattern));
        $this->assertIsString($employment->from);
        $this->assertEquals($this->expectedEmployment['to'], $employment->to);
        $this->assertTrue(is_array($employment->user) || is_object($employment->user));
    }

    public function testGetListWithFromParameter(): void
    {
        $expectedEmployments = [$this->expectedEmployment];
        $this->mockResponse(200, json_encode($expectedEmployments));
        $employments = $this->mocoClient->userEmployments->get(['from' => '2018-01-01']);

        $this->assertIsArray($employments);
        $this->assertCount(1, $employments);
        $this->assertInstanceOf(UserEmployment::class, $employments[0]);
        $this->assertEquals($this->expectedEmployment['id'], $employments[0]->id);
    }

    public function testGetListWithFromAndToParameters(): void
    {
        $expectedEmployments = [$this->expectedEmployment];
        $this->mockResponse(200, json_encode($expectedEmployments));
        $employments = $this->mocoClient->userEmployments->get(
            [
            'from' => '2018-01-01',
            'to' => '2018-12-31'
            ]
        );

        $this->assertIsArray($employments);
        $this->assertCount(1, $employments);
        $this->assertInstanceOf(UserEmployment::class, $employments[0]);
    }

    public function testGetListWithUserIdFilter(): void
    {
        $expectedEmployments = [$this->expectedEmployment];
        $this->mockResponse(200, json_encode($expectedEmployments));
        $employments = $this->mocoClient->userEmployments->get(
            [
            'from' => '2018-01-01',
            'user_id' => 933590696
            ]
        );

        $this->assertIsArray($employments);
        $this->assertCount(1, $employments);
        $this->assertInstanceOf(UserEmployment::class, $employments[0]);
    }

    public function testGetListWithoutFromParameter(): void
    {
        $expectedEmployments = [$this->expectedEmployment];
        $this->mockResponse(200, json_encode($expectedEmployments));
        $employments = $this->mocoClient->userEmployments->get(['user_id' => 123]);

        $this->assertIsArray($employments);
        $this->assertCount(1, $employments);
        $this->assertInstanceOf(UserEmployment::class, $employments[0]);
    }

    public function testGetListWithNullParameter(): void
    {
        $expectedEmployments = [$this->expectedEmployment];
        $this->mockResponse(200, json_encode($expectedEmployments));
        $employments = $this->mocoClient->userEmployments->get();

        $this->assertIsArray($employments);
        $this->assertCount(1, $employments);
        $this->assertInstanceOf(UserEmployment::class, $employments[0]);
    }

    public function testGetEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $employments = $this->mocoClient->userEmployments->get(['from' => '2018-01-01']);

        $this->assertIsArray($employments);
        $this->assertEmpty($employments);
    }

    public function testCreate(): void
    {
        $params = [
            'user_id' => 933590696,
            'pattern' => [
                'am' => [4, 4, 4, 4, 4],
                'pm' => [4, 4, 4, 4, 4]
            ]
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedEmployment, $params)));
        $employment = $this->mocoClient->userEmployments->create($params);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertTrue(is_array($employment->pattern) || is_object($employment->pattern));
    }

    public function testCreateWithOptionalFields(): void
    {
        $params = [
            'user_id' => 933590696,
            'pattern' => [
                'am' => [8, 8, 8, 8, 8],
                'pm' => [0, 0, 0, 0, 0]
            ],
            'from' => '2019-01-01',
            'to' => '2019-12-31'
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedEmployment, $params)));
        $employment = $this->mocoClient->userEmployments->create($params);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertTrue(is_array($employment->pattern) || is_object($employment->pattern));
        $this->assertIsString($employment->from);
        $this->assertIsString($employment->to);
    }

    public function testUpdate(): void
    {
        $employmentId = 123456;
        $params = [
            'pattern' => [
                'am' => [8, 8, 8, 8, 0],
                'pm' => [0, 0, 0, 0, 8]
            ]
        ];
        $updatedEmployment = array_merge($this->expectedEmployment, $params);

        $this->mockResponse(200, json_encode($updatedEmployment));
        $employment = $this->mocoClient->userEmployments->update($employmentId, $params);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertTrue(is_array($employment->pattern) || is_object($employment->pattern));
        $this->assertEquals($employmentId, $employment->id);
    }

    public function testUpdateDates(): void
    {
        $employmentId = 123456;
        $params = [
            'from' => '2019-06-01',
            'to' => '2019-12-31'
        ];
        $updatedEmployment = array_merge($this->expectedEmployment, $params);

        $this->mockResponse(200, json_encode($updatedEmployment));
        $employment = $this->mocoClient->userEmployments->update($employmentId, $params);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertIsString($employment->from);
        $this->assertIsString($employment->to);
    }

    public function testDelete(): void
    {
        $employmentId = 123456;
        $this->mockResponse(204, '');
        $result = $this->mocoClient->userEmployments->delete($employmentId);

        $this->assertNull($result);
    }

    public function testEntityAllProperties(): void
    {
        $this->mockResponse(200, json_encode($this->expectedEmployment));
        $employment = $this->mocoClient->userEmployments->get(123456);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertIsInt($employment->id);
        $this->assertIsNumeric($employment->weekly_target_hours);
        $this->assertTrue(is_array($employment->pattern) || is_object($employment->pattern));
        $this->assertIsString($employment->from);
        $this->assertIsString($employment->to);
        $this->assertTrue(is_array($employment->user) || is_object($employment->user));
        $this->assertIsString($employment->created_at);
        $this->assertIsString($employment->updated_at);
    }

    public function testPatternStructure(): void
    {
        $this->mockResponse(200, json_encode($this->expectedEmployment));
        $employment = $this->mocoClient->userEmployments->get(123456);

        $patternArray = (array) $employment->pattern;
        $this->assertArrayHasKey('am', $patternArray);
        $this->assertArrayHasKey('pm', $patternArray);
        $this->assertIsArray($patternArray['am']);
        $this->assertIsArray($patternArray['pm']);
        $this->assertCount(5, $patternArray['am']);
        $this->assertCount(5, $patternArray['pm']);
    }

    public function testUserStructure(): void
    {
        $this->mockResponse(200, json_encode($this->expectedEmployment));
        $employment = $this->mocoClient->userEmployments->get(123456);

        $userArray = (array) $employment->user;
        $this->assertArrayHasKey('id', $userArray);
        $this->assertArrayHasKey('firstname', $userArray);
        $this->assertArrayHasKey('lastname', $userArray);
        $this->assertIsInt($userArray['id']);
        $this->assertIsString($userArray['firstname']);
        $this->assertIsString($userArray['lastname']);
    }

    public function testEntityMandatoryFields(): void
    {
        $employment = new UserEmployment();
        $mandatoryFields = $employment->getMandatoryFields();

        $this->assertEquals(['user_id', 'pattern'], $mandatoryFields);
    }

    public function testServiceEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->mocoClient->userEmployments);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        $endpoint = $method->invoke($this->mocoClient->userEmployments);

        $this->assertStringContainsString('users/employments', $endpoint);
    }

    public function testNullToField(): void
    {
        $employmentWithNullTo = array_merge($this->expectedEmployment, ['to' => null]);
        $this->mockResponse(200, json_encode($employmentWithNullTo));
        $employment = $this->mocoClient->userEmployments->get(123456);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertNull($employment->to);
    }
}
