<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Offer;

use Moco\Entity\OfferCustomerApproval;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class OfferCustomerApprovalServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "approval_url" => "https://mycompany.mocoapp.com/offer_approval/1234567890abcdef",
        "offer_document_url" => "https://mycompany.mocoapp.com/offer_approval/1234567890abcdef/document",
        "active" => true,
        "customer_full_name" => "John Doe",
        "customer_email" => "john.doe@example.com",
        "signature_url" => "https://mycompany.mocoapp.com/signature_123.png",
        "signed_at" => "2024-01-20T10:30:00Z",
        "created_at" => "2024-01-15T09:00:00Z",
        "updated_at" => "2024-01-20T10:30:00Z"
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $approval = $this->mocoClient->offerCustomerApproval->get(12345);
        $this->assertInstanceOf(OfferCustomerApproval::class, $approval);
        $this->assertEquals(12345, $approval->id);
        $this->assertTrue($approval->active);
        $this->assertEquals("john.doe@example.com", $approval->customer_email);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->offerCustomerApproval->get(999999);
    }

    public function testActivate(): void
    {
        $activeResponse = $this->expectedResponse;
        $activeResponse['active'] = true;
        $activeResponse['customer_full_name'] = null;
        $activeResponse['customer_email'] = null;
        $activeResponse['signature_url'] = null;
        $activeResponse['signed_at'] = null;

        $this->mockResponse(200, json_encode($activeResponse));
        $approval = $this->mocoClient->offerCustomerApproval->activate(12345);
        $this->assertInstanceOf(OfferCustomerApproval::class, $approval);
        $this->assertEquals(12345, $approval->id);
        $this->assertTrue($approval->active);
        $this->assertNull($approval->customer_full_name);
        $this->assertNull($approval->signed_at);
    }

    public function testDeactivate(): void
    {
        $deactiveResponse = $this->expectedResponse;
        $deactiveResponse['active'] = false;

        $this->mockResponse(200, json_encode($deactiveResponse));
        $approval = $this->mocoClient->offerCustomerApproval->deactivate(12345);
        $this->assertInstanceOf(OfferCustomerApproval::class, $approval);
        $this->assertEquals(12345, $approval->id);
        $this->assertFalse($approval->active);
        $this->assertEquals("john.doe@example.com", $approval->customer_email);
    }
}
