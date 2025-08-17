<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Invoice;

use Moco\Entity\InvoiceBookkeepingExport;
use Tests\Functional\Service\AbstractServiceTest;

class InvoiceBookkeepingExportsServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'invoice_ids' => [123, 234], // These should be replaced with valid invoice IDs in actual testing
        'comment' => 'Test export - Functional Test',
        'trigger_submission' => false // Set to false to avoid actual DATEV submission in tests
    ];

    public function testCreate(): int
    {
        $export = $this->mocoClient->invoiceBookkeepingExports->create($this->createParams);
        $this->assertInstanceOf(InvoiceBookkeepingExport::class, $export);
        $this->assertEquals('Test export - Functional Test', $export->comment);
        $this->assertEquals([123, 234], $export->invoice_ids);
        return $export->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $exportId): int
    {
        $exports = $this->mocoClient->invoiceBookkeepingExports->get();
        $this->assertIsArray($exports);

        $export = $this->mocoClient->invoiceBookkeepingExports->get($exportId);
        $this->assertInstanceOf(InvoiceBookkeepingExport::class, $export);
        $this->assertEquals($exportId, $export->id);
        return $exportId;
    }

    /**
     * @depends testGet
     */
    public function testGetAll(int $exportId): void
    {
        // Test getting all exports with potential filtering
        $allExports = $this->mocoClient->invoiceBookkeepingExports->get([
            'from' => '2024-01-01',
            'to' => '2024-12-31'
        ]);
        $this->assertIsArray($allExports);

        // Verify our created export is in the list
        $found = false;
        foreach ($allExports as $export) {
            if ($export->id === $exportId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created export should be found in the list');
    }
}
