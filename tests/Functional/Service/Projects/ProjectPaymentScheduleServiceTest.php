<?php

namespace Functional\Service\Projects;

use Moco\Entity\ProjectPaymentSchedule;
use Tests\Functional\Service\AbstractServiceTest;

class ProjectPaymentScheduleServiceTest extends AbstractServiceTest
{
    public function testCreate(): array
    {
        $params = [
            'project_id' => 947556942,
            'net_total' => 1000.0,
            'date' => '2024-01-15',
            'title' => 'First payment',
            'checked' => false
        ];

        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->create($params);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals('First payment', $paymentSchedule->title);
        $this->assertEquals(1000.0, $paymentSchedule->net_total);
        return ['project_id' => 947556942, 'id' => $paymentSchedule->id];
    }

    /**
     * @depends testCreate
     */
    public function testGet(array $data): array
    {
        $paymentSchedules = $this->mocoClient->projectPaymentSchedule->get(['project_id' => $data['project_id']]);
        $this->assertIsArray($paymentSchedules);

        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->get($data);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals('First payment', $paymentSchedule->title);
        return $data;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(array $data): array
    {
        $data = array_merge($data, ['net_total' => 1500.0, 'title' => 'Updated payment']);
        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->update($data['id'], $data);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals(1500.0, $paymentSchedule->net_total);
        $this->assertEquals('Updated payment', $paymentSchedule->title);
        return $data;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(array $data): void
    {
        $this->assertNull($this->mocoClient->projectPaymentSchedule->delete($data['project_id'], $data['id']));
    }
}
