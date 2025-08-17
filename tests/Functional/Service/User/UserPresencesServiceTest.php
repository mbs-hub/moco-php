<?php

namespace Tests\Functional\Service\User;

use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class UserPresencesServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'date' => '2023-07-03',
        'from' => '09:00',
        'to' => '17:00',
        'is_home_office' => false
    ];

    public function testCreate(): int
    {
        $this->createParams['date'] = date('Y-m-d', strtotime('-1 day'));
        $presence = $this->mocoClient->userPresences->create($this->createParams);
        $this->assertEquals($this->createParams['date'], $presence->date);
        $this->assertEquals($this->createParams['from'], $presence->from);
        $this->assertEquals($this->createParams['to'], $presence->to);
        $this->assertFalse($presence->is_home_office);
        return $presence->id;
    }

    public function testCreateInvalidRequestException(): void
    {
        $params = $this->createParams;
        unset($params['date']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->userPresences->create($params);
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $presenceId): int
    {
        $presence = $this->mocoClient->userPresences->get($presenceId);
        $this->assertEquals($presenceId, $presence->id);
        $this->assertIsString($presence->date);
        $this->assertIsString($presence->from);

        $presences = $this->mocoClient->userPresences->get();
        $this->assertIsArray($presences);
        $this->assertNotEmpty($presences);

        return $presenceId;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $presenceId): int
    {
        $updateParams = ['to' => '18:00', 'is_home_office' => true];
        $presence = $this->mocoClient->userPresences->update($presenceId, $updateParams);
        $this->assertEquals('18:00', $presence->to);
        $this->assertTrue($presence->is_home_office);
        return $presenceId;
    }

    public function testTouch(): void
    {
        $presence = $this->mocoClient->userPresences->touch();
        $this->assertIsInt($presence->id);
        $this->assertEquals(date('Y-m-d'), $presence->date);
        $this->assertIsString($presence->from);

        // Clean up by stopping the presence
        if ($presence->to === null) {
            $this->mocoClient->userPresences->touch();
        }
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $presenceId): void
    {
        $this->assertNull($this->mocoClient->userPresences->delete($presenceId));
    }
}
