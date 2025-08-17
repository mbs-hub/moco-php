<?php

namespace Tests\Unit\Service\Projects;

use Moco\Entity\ProjectPaymentSchedule;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class ProjectPaymentScheduleServiceTest extends AbstractServiceTest
{
    public function testCreate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'net_total' => 1000.0,
            'date' => '2024-01-15',
            'title' => 'First payment',
            'checked' => false,
            'billed' => false
        ];
        $this->mockResponse(200, json_encode($params));
        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->create($params);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals('First payment', $paymentSchedule->title);

        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->projectPaymentSchedule->create([]);
    }

    public function testGet(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'net_total' => 1000.0,
            'date' => '2024-01-15',
            'title' => 'First payment',
            'checked' => false,
            'billed' => false
        ];

        $this->mockResponse(200, json_encode([$params]));
        $paymentSchedules = $this->mocoClient->projectPaymentSchedule->get(['project_id' => 123]);
        $this->assertIsArray($paymentSchedules);

        $this->mockResponse(200, json_encode($params));
        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->get(['project_id' => 123, 'id' => 1234]);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals('First payment', $paymentSchedule->title);

        $this->mockResponse(404, '');
        $this->expectException(NotFoundException::class);
        $this->mocoClient->projectPaymentSchedule->get(['project_id' => 12345]);
    }

    public function testUpdate(): void
    {
        $params = [
            'id' => 1234,
            'project_id' => 123,
            'net_total' => 1500.0,
            'date' => '2024-01-15',
            'title' => 'Updated payment',
            'checked' => true,
            'billed' => false
        ];
        $this->mockResponse(200, json_encode($params));
        $paymentSchedule = $this->mocoClient->projectPaymentSchedule->update($params['id'], $params);
        $this->assertInstanceOf(ProjectPaymentSchedule::class, $paymentSchedule);
        $this->assertEquals('Updated payment', $paymentSchedule->title);
        $this->assertEquals(1500.0, $paymentSchedule->net_total);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->projectPaymentSchedule->delete(123, 1234));
    }
}
