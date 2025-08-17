<?php

namespace Tests\Functional\Service;

use Functional\Service\Projects\ProjectTasksServiceTest;
use Moco\Entity\Activity;

class ActivitiesServiceTest extends AbstractServiceTest
{
    public function testCreate(): array
    {
        $projectTasksServiceTest = new ProjectTasksServiceTest();
        $result = $projectTasksServiceTest->testCreate();

        $params = [
            'date' => '2022-11-20',
            'description' => 'functional test',
            'project_id' => $result['project_id'],
            'task_id' => $result['id'],
            'hours' => 1,
        ];

        $activity = $this->mocoClient->activities->create($params);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($result['project_id'], $activity->project->id);
        $result['activity_id'] = $activity->id;
        $result['customer_id'] = $activity->customer->id;
        return $result;
    }

    /**
     * @depends testCreate
    */
    public function testGet(array $data): array
    {
        $activities = $this->mocoClient->activities->get();
        $this->assertIsArray($activities);

        $activity = $this->mocoClient->activities->get($data['activity_id']);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals($data['activity_id'], $activity->id);

        $activities = $this->mocoClient->activities->get(['ids' => (string)$data['activity_id']]);
        $this->assertIsArray($activities);
        $this->assertEquals($data['activity_id'], $activities[0]->id);

        return $data;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(array $data): array
    {
        $activity = $this->mocoClient->activities->update($data['activity_id'], ['description' => 'UPDATED']);
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('UPDATED', $activity->description);
        return $data;
    }

//    /**
//     * @depends testUpdate
//     */
//    public function testStartTimer(array $data): array
//    {
//        $this->markTestSkipped();
//        $activity = $this->mocoClient->activities->startTimer($data['activity_id']);
//        $this->assertInstanceOf(Activity::class, $activity);
//        return $data;
//    }
//
//    /**
//     * @depends testStartTimer
//     */
//    public function testStopTimer(array $data): array
//    {
//        $this->markTestSkipped();
//        $activity = $this->mocoClient->activities->stopTimer($data['activity_id']);
//        $this->assertInstanceOf(Activity::class, $activity);
//        return $data;
//    }

    /**
     * @depends testUpdate
     */
    public function testDelete(array $data)
    {
        $projectTasksServiceTest = new ProjectTasksServiceTest();
        $this->assertNull($this->mocoClient->activities->delete($data['activity_id']));
        $this->assertNull($projectTasksServiceTest->testDelete($data));
    }

    public function testDisregard(): void
    {
        $projectTasksServiceTest = new ProjectTasksServiceTest();
        $result = $projectTasksServiceTest->testCreate();

        $createParams = [
            'date' => '2022-11-20',
            'description' => 'functional test',
            'project_id' => $result['project_id'],
            'task_id' => $result['id'],
            'hours' => 1,
        ];

        $activity = $this->mocoClient->activities->create($createParams);
        $result['activity_id'] = $activity->id;
        $result['customer_id'] = $activity->customer->id;

        $params = [
            'activity_ids' => [$result['activity_id']],
            'reason' => 'functional test',
            'company_id' => $result['customer_id']
        ];

        $result = $this->mocoClient->activities->disregard($params);
        $this->assertIsArray($result);
    }
}
