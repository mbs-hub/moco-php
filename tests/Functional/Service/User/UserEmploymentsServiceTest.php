<?php

namespace Functional\Service\User;

use Moco\Entity\UserEmployment;
use Tests\Functional\Service\AbstractServiceTest;

class UserEmploymentsServiceTest extends AbstractServiceTest
{
    private int $testUserId = 933736932; // Use an existing test user ID

    public function testGetEmploymentsList(): void
    {
        $employments = $this->mocoClient->userEmployments->get();

        $this->assertIsArray($employments);

        if (!empty($employments)) {
            $this->assertInstanceOf(UserEmployment::class, $employments[0]);
            $this->assertIsInt($employments[0]->id);
            $this->assertIsFloat($employments[0]->weekly_target_hours);
            $this->assertIsString($employments[0]->from);
            $this->assertIsObject($employments[0]->user);
            $this->assertIsString($employments[0]->created_at);
            $this->assertIsString($employments[0]->updated_at);
        }

        $employments = $this->mocoClient->userEmployments->get(['from' => '2018-01-01']);
        $this->assertIsArray($employments);
        $employments = $this->mocoClient->userEmployments->get(['user_id' => 933736932]);
        $this->assertIsArray($employments);
        $employment = $this->mocoClient->userEmployments->get($employments[0]->id);
        ;
        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertEquals($employments[0]->id, $employment->id);
    }

    public function testCreateEmployment(): void
    {
        $employmentData = [
            'user_id' => $this->testUserId,
            'pattern' => [
                'am' => [4, 4, 4, 4, 4],
                'pm' => [4, 4, 4, 4, 4],
            ],
            'from' => '2026-01-01',
        ];

        $employment = $this->mocoClient->userEmployments->create($employmentData);

        $this->assertInstanceOf(UserEmployment::class, $employment);
        $this->assertEquals(933736932, $employment->user->id);
    }
}
