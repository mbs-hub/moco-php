<?php

namespace Tests\Functional\Service;

use Moco\Entity\VatCode;
use Moco\Exception\InvalidRequestException;

class VatCodeServiceTest extends AbstractServiceTest
{
    public function testGetAllSalesVatCodes(): void
    {
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        $this->assertIsArray($vatCodes);

        if (!empty($vatCodes)) {
            $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
            $this->assertIsInt($vatCodes[0]->id);
            $this->assertIsFloat($vatCodes[0]->tax);
            $this->assertIsBool($vatCodes[0]->reverse_charge);
            $this->assertIsBool($vatCodes[0]->intra_eu);
            $this->assertIsBool($vatCodes[0]->active);

            // Sales-specific properties
            $this->assertTrue(property_exists($vatCodes[0], 'print_gross_total'));
            $this->assertTrue(property_exists($vatCodes[0], 'credit_account'));
        }
    }

    public function testGetAllPurchasesVatCodes(): void
    {
        $vatCodes = $this->mocoClient->vatCodes->getPurchases();

        $this->assertIsArray($vatCodes);

        if (!empty($vatCodes)) {
            $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
            $this->assertIsInt($vatCodes[0]->id);
        }
    }

    public function testGetSaleVatCode(): void
    {
        // First get all sales VAT codes to get a valid ID
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        if (!empty($vatCodes)) {
            $firstVatCode = $vatCodes[0];
            $vatCodeId = $firstVatCode->id;

            $vatCode = $this->mocoClient->vatCodes->getSale($vatCodeId);

            $this->assertInstanceOf(VatCode::class, $vatCode);
            $this->assertEquals($vatCodeId, $vatCode->id);
        } else {
            $this->markTestSkipped('No sales VAT codes available to test with');
        }
    }

    public function testGetPurchaseVatCode(): void
    {
        // First get all purchases VAT codes to get a valid ID
        $vatCodes = $this->mocoClient->vatCodes->getPurchases();

        if (!empty($vatCodes)) {
            $firstVatCode = $vatCodes[0];
            $vatCodeId = $firstVatCode->id;

            $vatCode = $this->mocoClient->vatCodes->getPurchase($vatCodeId);

            $this->assertInstanceOf(VatCode::class, $vatCode);
            $this->assertEquals($vatCodeId, $vatCode->id);
        } else {
            $this->markTestSkipped('No purchases VAT codes available to test with');
        }
    }

    public function testGetSalesVatCodesWithActiveFilter(): void
    {
        $activeVatCodes = $this->mocoClient->vatCodes->getSales(['active' => true]);
        $inactiveVatCodes = $this->mocoClient->vatCodes->getSales(['active' => false]);

        $this->assertIsArray($activeVatCodes);
        $this->assertIsArray($inactiveVatCodes);

        // Check that active filter is working
        foreach ($activeVatCodes as $vatCode) {
            $this->assertTrue($vatCode->active, 'Active filter should return only active VAT codes');
        }

        foreach ($inactiveVatCodes as $vatCode) {
            $this->assertFalse($vatCode->active, 'Active=false filter should return only inactive VAT codes');
        }
    }

    public function testGetPurchasesVatCodesWithActiveFilter(): void
    {
        $activeVatCodes = $this->mocoClient->vatCodes->getPurchases(['active' => true]);
        $inactiveVatCodes = $this->mocoClient->vatCodes->getPurchases(['active' => false]);

        $this->assertIsArray($activeVatCodes);
        $this->assertIsArray($inactiveVatCodes);

        // Check that active filter is working
        foreach ($activeVatCodes as $vatCode) {
            $this->assertTrue($vatCode->active, 'Active filter should return only active VAT codes');
        }

        foreach ($inactiveVatCodes as $vatCode) {
            $this->assertFalse($vatCode->active, 'Active=false filter should return only inactive VAT codes');
        }
    }

    public function testGetSalesVatCodesWithReverseChargeFilter(): void
    {
        $reverseChargeVatCodes = $this->mocoClient->vatCodes->getSales(['reverse_charge' => true]);
        $normalVatCodes = $this->mocoClient->vatCodes->getSales(['reverse_charge' => false]);

        $this->assertIsArray($reverseChargeVatCodes);
        $this->assertIsArray($normalVatCodes);

        // Check that reverse_charge filter is working
        foreach ($reverseChargeVatCodes as $vatCode) {
            $this->assertTrue(
                $vatCode->reverse_charge,
                'Reverse charge filter should return only reverse charge VAT codes'
            );
        }

        foreach ($normalVatCodes as $vatCode) {
            $this->assertFalse(
                $vatCode->reverse_charge,
                'Reverse charge=false filter should return only normal VAT codes'
            );
        }
    }

    public function testGetSalesVatCodesWithIntraEuFilter(): void
    {
        $intraEuVatCodes = $this->mocoClient->vatCodes->getSales(['intra_eu' => true]);
        $domesticVatCodes = $this->mocoClient->vatCodes->getSales(['intra_eu' => false]);

        $this->assertIsArray($intraEuVatCodes);
        $this->assertIsArray($domesticVatCodes);

        // Check that intra_eu filter is working
        foreach ($intraEuVatCodes as $vatCode) {
            $this->assertTrue($vatCode->intra_eu, 'Intra EU filter should return only intra EU VAT codes');
        }

        foreach ($domesticVatCodes as $vatCode) {
            $this->assertFalse($vatCode->intra_eu, 'Intra EU=false filter should return only domestic VAT codes');
        }
    }

    public function testGetPurchasesVatCodesWithIdsFilter(): void
    {
        $this->markTestSkipped();
        // First get some purchases VAT codes to get valid IDs
        $allVatCodes = $this->mocoClient->vatCodes->getPurchases();

        if (count($allVatCodes) >= 2) {
            $firstTwoIds = [
                $allVatCodes[0]->id,
                $allVatCodes[1]->id
            ];

            $filteredVatCodes = $this->mocoClient->vatCodes->getPurchases(['ids' => $firstTwoIds]);

            $this->assertIsArray($filteredVatCodes);
            $this->assertCount(2, $filteredVatCodes);

            $returnedIds = array_map(fn($vatCode) => $vatCode->id, $filteredVatCodes);
            $this->assertEquals(sort($firstTwoIds), sort($returnedIds));
        } else {
            $this->markTestSkipped('Need at least 2 purchases VAT codes to test IDs filter');
        }
    }

    public function testSpecificMethods(): void
    {
        // Test using the specific methods
        $salesVatCodes = $this->mocoClient->vatCodes->getSales();
        $purchasesVatCodes = $this->mocoClient->vatCodes->getPurchases();

        $this->assertIsArray($salesVatCodes);
        $this->assertIsArray($purchasesVatCodes);

        if (!empty($salesVatCodes)) {
            $this->assertInstanceOf(VatCode::class, $salesVatCodes[0]);

            // Test getById with specific method
            $vatCode = $this->mocoClient->vatCodes->getSale($salesVatCodes[0]->id);
            $this->assertInstanceOf(VatCode::class, $vatCode);
            $this->assertEquals($salesVatCodes[0]->id, $vatCode->id);
        }
    }

    public function testComplexFiltering(): void
    {
        // Test combining multiple filters
        $filters = [
            'active' => true,
            'reverse_charge' => false,
            'intra_eu' => false
        ];

        $salesVatCodes = $this->mocoClient->vatCodes->getSales($filters);
        $purchasesVatCodes = $this->mocoClient->vatCodes->getPurchases($filters);

        $this->assertIsArray($salesVatCodes);
        $this->assertIsArray($purchasesVatCodes);

        // Verify all returned VAT codes match the filters
        foreach ($salesVatCodes as $vatCode) {
            $this->assertTrue($vatCode->active);
            $this->assertFalse($vatCode->reverse_charge);
            $this->assertFalse($vatCode->intra_eu);
        }

        foreach ($purchasesVatCodes as $vatCode) {
            $this->assertTrue($vatCode->active);
            $this->assertFalse($vatCode->reverse_charge);
            $this->assertFalse($vatCode->intra_eu);
        }
    }

    public function testVatCodeEntityProperties(): void
    {
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        if (!empty($vatCodes)) {
            $vatCode = $vatCodes[0];

            // Test that all expected properties exist
            $this->assertTrue(property_exists($vatCode, 'id'));
            $this->assertTrue(property_exists($vatCode, 'tax'));
            $this->assertTrue(property_exists($vatCode, 'reverse_charge'));
            $this->assertTrue(property_exists($vatCode, 'intra_eu'));
            $this->assertTrue(property_exists($vatCode, 'active'));
            $this->assertTrue(property_exists($vatCode, 'code'));

            // Test mandatory fields method
            $this->assertEquals([], $vatCode->getMandatoryFields());
        }
    }
}
