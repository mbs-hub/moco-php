<?php

namespace Tests\Unit\Service;

use Moco\Entity\Activity;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;

class ActivitiesServiceTest extends AbstractServiceTest
{
    private array $params = [
        'date'        => '2022-11-20',
        'description' => 'unit test',
        'project_id'  => 123,
        'task_id'     => 1234,
        'hours'       => 1,
    ];

    public function testCreate(): void
    {
        $params = $this->params;

        $this->mockResponse(200, json_encode($params));
        $activity = $this->mocoClient->activities->create($params);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('unit test', $activity->description);

        unset($params['project_id']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->activities->create($params);
    }

    public function testGet(): void
    {
        $params = $this->params;
        $this->mockResponse(200, json_encode([$params]));
        $activities = $this->mocoClient->activities->get();
        $this->assertIsArray($activities);
        $this->assertEquals('unit test', $activities[0]->description);

        $this->mockResponse(200, json_encode($params));
        $activity = $this->mocoClient->activities->get(12345);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('unit test', $activity->description);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->activities->get(123);
    }

    public function testUpdate(): void
    {
        $params = $this->params;
        $params['description'] = 'description updated';
        $this->mockResponse(200, json_encode($params));
        $activity = $this->mocoClient->activities->update(123, $params);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('description updated', $activity->description);
    }

    public function testStartTimer(): void
    {
        $this->mockResponse(200, json_encode($this->params));
        $activity = $this->mocoClient->activities->startTimer(123);
        $this->assertInstanceOf(Activity::class, $activity);

        $this->mockResponse(422);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->activities->startTimer(1234);
    }

    public function testStopTimer(): void
    {
        $this->mockResponse(200, json_encode($this->params));
        $activity = $this->mocoClient->activities->stopTimer(123);
        $this->assertInstanceOf(Activity::class, $activity);

        $this->mockResponse(422);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->activities->stopTimer(1234);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->activities->delete(123));
    }

    public function testDisregard(): void
    {
        $params = [
            'activity_ids' => [123, 1234],
            'reason'       => 'unit test',
            'company_id'   => 12345,
        ];
        $this->mockResponse(200, json_encode([$this->params]));
        $result = $this->mocoClient->activities->disregard($params);
        $this->assertIsArray($result);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->activities->disregard(
            [
                'reason' => 'test exception',
            ]
        );
    }
}
