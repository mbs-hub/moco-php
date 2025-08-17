<?php

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectTask;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectTasksServiceTest extends AbstractServiceTest
{
    public function testCreate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'name' => 'task',
            'billable' => true,
            'active' => true,
            'budget' => 5000,
            'hourly_rate' => 120
        ];
        $this->mockResponse(200, json_encode($params));
        $task = $this->mocoClient->projectTasks->create($params);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals('task', $task->name);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->projectTasks->create([]);
    }

    public function testGet(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'name' => 'task',
            'billable' => true,
            'active' => true,
            'budget' => 5000,
            'hourly_rate' => 120
        ];

        $this->mockResponse(200, json_encode([$params]));
        $tasks = $this->mocoClient->projectTasks->get(['project_id' => 123]);
        $this->assertIsArray($tasks);

        $this->mockResponse(200, json_encode($params));
        $task = $this->mocoClient->projectTasks->get(['project_id' => 123, 'id' => 1234]);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals('task', $task->name);

        $this->mockResponse(404, '');
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectTasks->get(['project_id' => 12345]);
    }

    public function testUpdate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'name' => 'name changed',
            'billable' => true,
            'active' => true,
            'budget' => 5000,
            'hourly_rate' => 120
        ];
        $this->mockResponse(200, json_encode($params));
        $task = $this->mocoClient->projectTasks->update($params['id'], $params);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals('name changed', $task->name);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->projectTasks->delete(123, 1234));
    }

    public function testDestroyAll(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->projectTasks->destroyAll(123));
    }
}
