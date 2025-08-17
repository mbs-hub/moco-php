<?php

declare(strict_types=1);

namespace Functional\Service\Projects;

use Moco\Entity\ProjectGroup;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectGroupsServiceTest extends AbstractServiceTest
{
    public function testGetAll(): void
    {
        $projectGroups = $this->mocoClient->projectGroups->get([]);
        $this->assertIsArray($projectGroups);

        if (count($projectGroups) > 0) {
            $this->assertInstanceOf(ProjectGroup::class, $projectGroups[0]);
            $this->assertNotNull($projectGroups[0]->id);
            $this->assertNotNull($projectGroups[0]->name);

            // Test that we can retrieve individual project group
            $singleGroup = $this->mocoClient->projectGroups->get($projectGroups[0]->id);
            $this->assertInstanceOf(ProjectGroup::class, $singleGroup);
            $this->assertEquals($projectGroups[0]->id, $singleGroup->id);
            $this->assertEquals($projectGroups[0]->name, $singleGroup->name);
        } else {
            $this->markTestSkipped('No project groups available for testing');
        }
    }

    public function testGetAllWithUserFilter(): void
    {
        if (!$this->leaderId) {
            $this->markTestSkipped('LEADER_ID environment variable not set');
        }

        $projectGroups = $this->mocoClient->projectGroups->get(['user_id' => $this->leaderId]);
        $this->assertIsArray($projectGroups);

        // Verify all returned groups belong to the specified user
        foreach ($projectGroups as $group) {
            $this->assertInstanceOf(ProjectGroup::class, $group);
            $this->assertNotNull($group->id);
            $this->assertNotNull($group->name);
            if (isset($group->user['id'])) {
                $this->assertEquals($this->leaderId, $group->user['id']);
            }
        }
    }

    public function testGetSingle(): void
    {
        // First get all project groups to find one we can test with
        $projectGroups = $this->mocoClient->projectGroups->get([]);

        if (count($projectGroups) === 0) {
            $this->markTestSkipped('No project groups available for testing');
        }

        $testGroupId = $projectGroups[0]->id;
        $projectGroup = $this->mocoClient->projectGroups->get($testGroupId);

        $this->assertInstanceOf(ProjectGroup::class, $projectGroup);
        $this->assertEquals($testGroupId, $projectGroup->id);
        $this->assertNotNull($projectGroup->name);
        $this->assertNotNull($projectGroup->created_at);
        $this->assertNotNull($projectGroup->updated_at);

        // Verify structure of nested objects
        if (isset($projectGroup->user)) {
            $this->assertIsArray($projectGroup->user);
            $this->assertArrayHasKey('id', $projectGroup->user);
        }

        if (isset($projectGroup->company)) {
            $this->assertIsArray($projectGroup->company);
            $this->assertArrayHasKey('id', $projectGroup->company);
            $this->assertArrayHasKey('name', $projectGroup->company);
        }

        if (isset($projectGroup->projects)) {
            $this->assertIsArray($projectGroup->projects);
            foreach ($projectGroup->projects as $project) {
                $this->assertIsArray($project);
                $this->assertArrayHasKey('id', $project);
            }
        }
    }

    public function testGetNonExistent(): void
    {
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->projectGroups->get(999999999);
    }

    public function testGetAllWithCompanyFilter(): void
    {
        // First get all project groups to find a company ID we can filter by
        $projectGroups = $this->mocoClient->projectGroups->get([]);

        if (count($projectGroups) === 0) {
            $this->markTestSkipped('No project groups available for testing');
        }

        $companyId = null;
        foreach ($projectGroups as $group) {
            if (isset($group->company['id'])) {
                $companyId = $group->company['id'];
                break;
            }
        }

        if (!$companyId) {
            $this->markTestSkipped('No project groups with company information available for testing');
        }

        $filteredGroups = $this->mocoClient->projectGroups->get(['company_id' => $companyId]);
        $this->assertIsArray($filteredGroups);

        // Verify all returned groups belong to the specified company
        foreach ($filteredGroups as $group) {
            $this->assertInstanceOf(ProjectGroup::class, $group);
            if (isset($group->company['id'])) {
                $this->assertEquals($companyId, $group->company['id']);
            }
        }
    }

    public function testGetAllEmptyFilter(): void
    {
        // Test with empty array parameter
        $projectGroups = $this->mocoClient->projectGroups->get([]);
        $this->assertIsArray($projectGroups);

        // Test with no parameter
        $projectGroupsNoParam = $this->mocoClient->projectGroups->get();
        $this->assertIsArray($projectGroupsNoParam);

        // Should return the same results
        $this->assertCount(count($projectGroups), $projectGroupsNoParam);
    }
}
