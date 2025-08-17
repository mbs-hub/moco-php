<?php

namespace Functional\Service\Projects;

use Moco\Entity\ProjectTask;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectTasksServiceTest extends AbstractServiceTest
{
    public function testCreate(): array
    {
        $projectsServiceTest = new ProjectsServiceTest();
        $project = $projectsServiceTest->testCreate();

        $params = [
            'project_id' => $project->id,
            'name' => 'task',
            'billable' => true,
            'active' => true,
            'budget' => 5000,
            'hourly_rate' => 120
        ];

        $task = $this->mocoClient->projectTasks->create($params);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals('task', $task->name);
        return ['project_id' => $project->id, 'id' => $task->id];
    }

    /**
     * @depends testCreate
     */
    public function testGet(array $data): array
    {
        $tasks = $this->mocoClient->projectTasks->get(['project_id' => $data['project_id']]);
        $this->assertIsArray($tasks);

        $task = $this->mocoClient->projectTasks->get($data);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals('task', $task->name);
        return $data;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(array $data): array
    {
        $data = array_merge($data, ['budget' => 1000]);
        $task = $this->mocoClient->projectTasks->update($data['id'], $data);
        $this->assertInstanceOf(ProjectTask::class, $task);
        $this->assertEquals(1000, $task->budget);
        return $data;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(array $data): void
    {
        $this->assertNull($this->mocoClient->projectTasks->delete($data['project_id'], $data['id']));
        ;

        $this->assertNull($this->mocoClient->projectTasks->destroyAll($data['project_id']));
        $projectsServiceTest = new ProjectsServiceTest();
        $projectsServiceTest->testDelete($data['project_id']);
    }
}
