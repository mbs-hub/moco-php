<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Offer;

use Tests\Functional\Service\AbstractServiceTest;

class OfferCustomerApprovalServiceTest extends AbstractServiceTest
{
    private int $testOfferId = 1688096; // This should be replaced with a valid offer ID in actual testing

    public function testActivate(): void
    {
        $approval = $this->mocoClient->offerCustomerApproval->activate($this->testOfferId);
        $this->assertTrue($approval->active);
        $this->assertNotEmpty($approval->approval_url);
        $this->assertNotEmpty($approval->offer_document_url);
    }

    /**
     * @depends testActivate
     */
    public function testGet(): void
    {
        $approval = $this->mocoClient->offerCustomerApproval->get($this->testOfferId);
        $this->assertTrue($approval->active);
        $this->assertNotEmpty($approval->approval_url);
        $this->assertNotEmpty($approval->offer_document_url);
    }

    /**
     * @depends testGet
     */
    public function testDeactivate(): void
    {
        $approval = $this->mocoClient->offerCustomerApproval->deactivate($this->testOfferId);
        $this->assertFalse($approval->active);
    }

    /**
     * @depends testDeactivate
     */
    public function testGetAfterDeactivation(): void
    {
        try {
            $approval = $this->mocoClient->offerCustomerApproval->get($this->testOfferId);
            // If we get here, the approval still exists but should be inactive
            $this->assertFalse($approval->active);
        } catch (\Moco\Exception\NotFoundException $e) {
            // This is also expected behavior after deactivation
            $this->assertTrue(true, 'Customer approval not found after deactivation, which is expected');
        }
    }

    /**
     * @depends testGetAfterDeactivation
     */
    public function testReactivate(): void
    {
        // Test that we can reactivate after deactivation
        $approval = $this->mocoClient->offerCustomerApproval->activate($this->testOfferId);
        $this->assertTrue($approval->active);

        // Clean up by deactivating again
        $this->mocoClient->offerCustomerApproval->deactivate($this->testOfferId);
    }
}
