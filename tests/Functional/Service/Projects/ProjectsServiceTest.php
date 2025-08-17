<?php

namespace Functional\Service\Projects;

use Moco\Entity\Project;
use Tests\Functional\Service\AbstractServiceTest;
use Tests\Functional\Service\CompaniesServiceTest;

class ProjectsServiceTest extends AbstractServiceTest
{
    public function testCreate(): Project
    {
        $companiesServiceTest = new CompaniesServiceTest();
        $companyId = $companiesServiceTest->testCreate();
        $params =
            [
                "name"        => "Moco-PHP",
                "currency"    => "EUR",
                "leader_id"   => $this->leaderId,
                "customer_id" => $companyId,
                "tags"        => ["Print", "Digital"],
            ];
        $project = $this->mocoClient->projects->create($params);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($project->name, $params['name']);
        return $project;
    }

    /**
     * @depends testCreate
     */
    public function testGet(Project $project): int
    {
        $projects = $this->mocoClient->projects->get();
        $this->assertIsArray($projects);

        $result = $this->mocoClient->projects->get(['identifier' => $project->identifier]);
        $this->assertEquals($project->identifier, $result[0]->identifier);

        $singleProject = $this->mocoClient->projects->get($project->id);
        $this->assertEquals($project->id, $singleProject->id);
        return $project->id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $projectId): int
    {
        $project = $this->mocoClient->projects->update($projectId, ['name' => 'name updated']);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals('name updated', $project->name);
        return $projectId;
    }

    public function testAssignedProjects()
    {
        $result = $this->mocoClient->projects->getAssignedProjects();
        $this->assertIsArray($result);
    }

    /**
     * @depends testUpdate
     */
    public function testArchive(int $projectId): int
    {
        $result = $this->mocoClient->projects->archive($projectId);
        $this->assertEquals($projectId, $result->id);
        return $projectId;
    }

    /**
     * @depends testArchive
     */
    public function testUnarchive(int $projectId): int
    {
        $project = $this->mocoClient->projects->unarchive($projectId);
        $this->assertEquals($projectId, $project->id);
        return $projectId;
    }

    /**
     * @depends testUnarchive
     */
    public function testReport(int $projectId): int
    {
        $report = $this->mocoClient->projects->report($projectId);
        $this->assertIsObject($report);
        return $projectId;
    }

    /**
     * @depends testReport
     */
    public function testDelete(int $projectId): void
    {
        $project = $this->mocoClient->projects->get($projectId);
        $this->assertNull($this->mocoClient->projects->delete($projectId));
        $this->assertNull($this->mocoClient->companies->delete($project->customer->id));
    }
}
