<?php

namespace Tests\Unit\Service;

use Moco\Entity\VatCode;
use Moco\Exception\InvalidRequestException;

class VatCodeServiceTest extends AbstractServiceTest
{
    private array $expectedSalesVatCode = [
        'id' => 186,
        'tax' => 7.7,
        'reverse_charge' => false,
        'intra_eu' => false,
        'active' => true,
        'print_gross_total' => true,
        'code' => '9',
        'credit_account' => '4000'
    ];

    private array $expectedPurchasesVatCode = [
        'id' => 186,
        'tax' => 7.7,
        'reverse_charge' => false,
        'intra_eu' => false,
        'active' => true,
        'code' => '9'
    ];

    public function testGetSales(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedSalesVatCode]));
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        $this->assertIsArray($vatCodes);
        $this->assertCount(1, $vatCodes);
        $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
        $this->assertEquals($this->expectedSalesVatCode['id'], $vatCodes[0]->id);
        $this->assertEquals($this->expectedSalesVatCode['tax'], $vatCodes[0]->tax);
        $this->assertEquals($this->expectedSalesVatCode['code'], $vatCodes[0]->code);
        $this->assertTrue(property_exists($vatCodes[0], 'print_gross_total'));
        $this->assertTrue(property_exists($vatCodes[0], 'credit_account'));
    }

    public function testGetPurchases(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedPurchasesVatCode]));
        $vatCodes = $this->mocoClient->vatCodes->getPurchases();

        $this->assertIsArray($vatCodes);
        $this->assertCount(1, $vatCodes);
        $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
        $this->assertEquals($this->expectedPurchasesVatCode['id'], $vatCodes[0]->id);
        $this->assertEquals($this->expectedPurchasesVatCode['tax'], $vatCodes[0]->tax);
        $this->assertEquals($this->expectedPurchasesVatCode['code'], $vatCodes[0]->code);
    }

    public function testGetSalesWithFilters(): void
    {
        $filters = ['active' => true, 'reverse_charge' => false];
        $this->mockResponse(200, json_encode([$this->expectedSalesVatCode]));
        $vatCodes = $this->mocoClient->vatCodes->getSales($filters);

        $this->assertIsArray($vatCodes);
        $this->assertCount(1, $vatCodes);
        $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
    }

    public function testGetPurchasesWithFilters(): void
    {
        $filters = ['active' => true, 'intra_eu' => false, 'ids' => [186, 187]];
        $this->mockResponse(200, json_encode([$this->expectedPurchasesVatCode]));
        $vatCodes = $this->mocoClient->vatCodes->getPurchases($filters);

        $this->assertIsArray($vatCodes);
        $this->assertCount(1, $vatCodes);
        $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
    }

    public function testGetSale(): void
    {
        $vatCodeId = 186;
        $this->mockResponse(200, json_encode($this->expectedSalesVatCode));
        $vatCode = $this->mocoClient->vatCodes->getSale($vatCodeId);

        $this->assertInstanceOf(VatCode::class, $vatCode);
        $this->assertEquals($this->expectedSalesVatCode['id'], $vatCode->id);
        $this->assertEquals($this->expectedSalesVatCode['tax'], $vatCode->tax);
        $this->assertEquals($this->expectedSalesVatCode['code'], $vatCode->code);
        $this->assertTrue(property_exists($vatCode, 'print_gross_total'));
        $this->assertTrue(property_exists($vatCode, 'credit_account'));
    }

    public function testGetPurchase(): void
    {
        $vatCodeId = 186;
        $this->mockResponse(200, json_encode($this->expectedPurchasesVatCode));
        $vatCode = $this->mocoClient->vatCodes->getPurchase($vatCodeId);

        $this->assertInstanceOf(VatCode::class, $vatCode);
        $this->assertEquals($this->expectedPurchasesVatCode['id'], $vatCode->id);
        $this->assertEquals($this->expectedPurchasesVatCode['tax'], $vatCode->tax);
        $this->assertEquals($this->expectedPurchasesVatCode['code'], $vatCode->code);
    }

    public function testGetSalesUsesPrivateGetMethod(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedSalesVatCode]));
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        $this->assertIsArray($vatCodes);
        $this->assertCount(1, $vatCodes);
        $this->assertInstanceOf(VatCode::class, $vatCodes[0]);
    }

    public function testGetByIdWithValidType(): void
    {
        $this->mockResponse(200, json_encode($this->expectedSalesVatCode));
        $vatCode = $this->mocoClient->vatCodes->getSale(186);

        $this->assertInstanceOf(VatCode::class, $vatCode);
        $this->assertEquals(186, $vatCode->id);
    }

    public function testGetEmptyArray(): void
    {
        $this->mockResponse(200, json_encode([]));
        $vatCodes = $this->mocoClient->vatCodes->getSales();

        $this->assertIsArray($vatCodes);
        $this->assertEmpty($vatCodes);
    }

    public function testEntityMandatoryFields(): void
    {
        $vatCode = new VatCode();
        $mandatoryFields = $vatCode->getMandatoryFields();

        $this->assertEquals([], $mandatoryFields);
    }

    public function testServiceEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->mocoClient->vatCodes);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        $endpoint = $method->invoke($this->mocoClient->vatCodes);

        $this->assertStringContainsString('vat_code_', $endpoint);
    }

    public function testBooleanFilterConversion(): void
    {
        // Test that boolean filters are properly converted to strings for API
        $filters = ['active' => true, 'reverse_charge' => false];
        $this->mockResponse(200, json_encode([$this->expectedSalesVatCode]));

        // This should not throw an exception and properly convert booleans
        $vatCodes = $this->mocoClient->vatCodes->getSales($filters);

        $this->assertIsArray($vatCodes);
    }

    public function testVatCodeProperties(): void
    {
        $this->mockResponse(200, json_encode($this->expectedSalesVatCode));
        $vatCode = $this->mocoClient->vatCodes->getSale(186);

        $this->assertInstanceOf(VatCode::class, $vatCode);
        $this->assertIsInt($vatCode->id);
        $this->assertIsFloat($vatCode->tax);
        $this->assertIsBool($vatCode->reverse_charge);
        $this->assertIsBool($vatCode->intra_eu);
        $this->assertIsBool($vatCode->active);
        $this->assertIsString($vatCode->code);
    }
}
