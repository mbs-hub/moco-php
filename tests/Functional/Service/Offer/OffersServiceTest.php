<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Offer;

use Moco\Entity\Offer;
use Tests\Functional\Service\AbstractServiceTest;

class OffersServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'recipient_address' => "Test Company\nTest Street 123\n12345 Test City",
        'date' => '2024-01-20',
        'due_date' => '2024-02-20',
        'title' => 'Test Offer - Functional Test',
        'tax' => 19.0,
        'items' => [
            [
                'type' => 'item',
                'title' => 'Consulting Services',
                'quantity' => 20.0,
                'unit' => 'h',
                'unit_price' => 150.0,
                'net_total' => 3000.0
            ]
        ],
        'currency' => 'EUR',
        'tags' => ['Consulting', 'Test']
    ];

    public function testCreate(): int
    {
        $offer = $this->mocoClient->offers->create($this->createParams);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals('Test Offer - Functional Test', $offer->title);
        $this->assertEquals('EUR', $offer->currency);
        return $offer->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $offerId): int
    {
        $offers = $this->mocoClient->offers->get();
        $this->assertIsArray($offers);

        $offer = $this->mocoClient->offers->get($offerId);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals($offerId, $offer->id);
        return $offerId;
    }

    /**
     * @depends testGet
     */
    public function testGetWithFilters(int $offerId): int
    {
        // Test getting offers with date filter
        $filteredOffers = $this->mocoClient->offers->get([
            'from' => '2024-01-01',
            'to' => '2024-12-31'
        ]);
        $this->assertIsArray($filteredOffers);

        // Verify our created offer is in the filtered results
        $found = false;
        foreach ($filteredOffers as $offer) {
            if ($offer->id === $offerId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created offer should be found in filtered results');

        return $offerId;
    }

    /**
     * @depends testGetWithFilters
     */
    public function testAssign(int $offerId): int
    {
        // Test assigning offer to a company (using a test company ID)
        $assignParams = [
            'company_id' => 762610111 // This should be replaced with a valid company ID in actual testing
        ];

        $offer = $this->mocoClient->offers->assign($offerId, $assignParams);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals($offerId, $offer->id);

        return $offerId;
    }

    /**
     * @depends testAssign
     */
    public function testUpdateStatus(int $offerId): int
    {
        $this->assertNull($this->mocoClient->offers->updateStatus($offerId, 'sent'));
        return $offerId;
    }

    /**
     * @depends testUpdateStatus
     */
    public function testGetPdf(int $offerId): int
    {
        $pdfContent = $this->mocoClient->offers->getPdf($offerId);
        $this->assertStringStartsWith('%PDF', $pdfContent);
        return $offerId;
    }

    /**
     * @depends testGetPdf
     */
    public function testGetAttachments(int $offerId): int
    {
        $attachments = $this->mocoClient->offers->getAttachments($offerId);
        $this->assertIsArray($attachments);
        return $offerId;
    }

    /**
     * @depends testGetAttachments
     */
    public function testSendEmail(int $offerId): int
    {
        // Note: This test might actually send an email in a real environment
        // Consider skipping this in production or using test email addresses
        if (getenv('SKIP_EMAIL_TESTS') === 'true') {
            $this->markTestSkipped('Email tests skipped by environment variable');
        }

        $emailParams = [
            'subject' => 'Test Offer Email',
            'text' => 'This is a test offer email from functional tests.',
            'emails_to' => 'test@example.com'
        ];

        $email = $this->mocoClient->offers->sendEmail($offerId, $emailParams);
        $this->assertEquals($emailParams['text'], $email->text);

        return $offerId;
    }

    /**
     * @depends testSendEmail
     */
    public function testGetByStatus(int $offerId): int
    {
        // Test getting offers filtered by status
        $sentOffers = $this->mocoClient->offers->get([
            'status' => 'sent'
        ]);
        $this->assertIsArray($sentOffers);

        // Verify our created offer is in the status-specific results
        $found = false;
        foreach ($sentOffers as $offer) {
            if ($offer->id === $offerId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created offer should be found when filtering by status');

        return $offerId;
    }
}
