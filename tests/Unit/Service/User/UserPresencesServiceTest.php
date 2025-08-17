<?php

namespace Tests\Unit\Service\User;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class UserPresencesServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'date' => '2018-07-03',
        'from' => '07:30',
        'to' => '13:15',
        'is_home_office' => true
    ];

    private array $responseData = [
        'id' => 982237015,
        'date' => '2018-07-03',
        'from' => '07:30',
        'to' => '13:15',
        'is_home_office' => true,
        'user' => [
            'id' => 933590696,
            'firstname' => 'John',
            'lastname' => 'Doe'
        ],
        'created_at' => '2018-07-03T07:30:00+02:00',
        'updated_at' => '2018-07-03T13:15:00+02:00'
    ];

    public function testCreate(): void
    {
        $this->mockResponse(200, json_encode($this->responseData));
        $presence = $this->mocoClient->userPresences->create($this->createParams);
        $this->assertEquals(982237015, $presence->id);
        $this->assertEquals('2018-07-03', $presence->date);
        $this->assertEquals('07:30', $presence->from);
        $this->assertEquals('13:15', $presence->to);
        $this->assertTrue($presence->is_home_office);
    }

    public function testCreateInvalidRequestException(): void
    {
        $params = $this->createParams;
        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userPresences->create($params);
    }

    public function testCreateMissingFromException(): void
    {
        $params = $this->createParams;
        unset($params['from']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userPresences->create($params);
    }

    public function testCreateInvalidResponseException(): void
    {
        $this->mockResponse(500);
        $this->expectException(InvalidResponseException::class);
        $this->mocoClient->userPresences->create($this->createParams);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->responseData));
        $presence = $this->mocoClient->userPresences->get(982237015);
        $this->assertEquals(982237015, $presence->id);
        $this->assertEquals('2018-07-03', $presence->date);

        $this->mockResponse(200, json_encode([$this->responseData]));
        $presences = $this->mocoClient->userPresences->get();
        $this->assertIsArray($presences);
        $this->assertEquals(982237015, $presences[0]->id);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->userPresences->get(99999);
    }

    public function testUpdate(): void
    {
        $updateParams = ['to' => '17:00'];
        $updatedData = array_merge($this->responseData, $updateParams);
        $this->mockResponse(200, json_encode($updatedData));

        $presence = $this->mocoClient->userPresences->update(982237015, $updateParams);
        $this->assertEquals('17:00', $presence->to);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->userPresences->update(99999, $updateParams);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->userPresences->delete(982237015));
    }

    public function testTouch(): void
    {
        $touchData = [
            'id' => 982237016,
            'date' => date('Y-m-d'),
            'from' => date('H:i'),
            'to' => null,
            'is_home_office' => false,
            'user' => [
                'id' => 933590696,
                'firstname' => 'John',
                'lastname' => 'Doe'
            ]
        ];

        $this->mockResponse(200, json_encode($touchData));
        $presence = $this->mocoClient->userPresences->touch();
        $this->assertEquals(982237016, $presence->id);
        $this->assertEquals(date('Y-m-d'), $presence->date);
        $this->assertNull($presence->to);
    }
}
