<?php

declare(strict_types=1);

namespace Tests\Functional\Service\Purchases;

use Moco\Entity\PurchaseBookkeepingExport;
use Tests\Functional\Service\AbstractServiceTest;

class PurchaseBookkeepingExportsServiceTest extends AbstractServiceTest
{
    public function testCreate(): int
    {
        $export = $this->mocoClient->purchaseBookkeepingExports->create(['purchase_ids' => [3139861], 'comment' => 'Test export - Functional Test']);
        ;
        $this->assertInstanceOf(PurchaseBookkeepingExport::class, $export);
        $this->assertEquals('Test export - Functional Test', $export->comment);
        $this->assertEquals([3139861], $export->purchase_ids);
        return $export->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $exportId): int
    {
        $exports = $this->mocoClient->purchaseBookkeepingExports->get();
        $this->assertIsArray($exports);

        $export = $this->mocoClient->purchaseBookkeepingExports->get($exportId);
        $this->assertInstanceOf(PurchaseBookkeepingExport::class, $export);
        $this->assertEquals($exportId, $export->id);
        return $exportId;
    }

    /**
     * @depends testGet
     */
    public function testGetAll(int $exportId): void
    {
        // Test getting all exports with potential filtering
        $allExports = $this->mocoClient->purchaseBookkeepingExports->get([
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
