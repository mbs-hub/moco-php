<?php

namespace Tests\Unit\Service;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;

class ReceiptsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123,
        "title" => "Office supplies",
        "date" => "2023-12-01",
        "billable" => true,
        "gross_total" => 150.75,
        "currency" => "EUR",
        "user" => [
            "id" => 456,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "project" => [
            "id" => 789,
            "name" => "Example Project"
        ],
        "items" => [
            [
                "id" => 1,
                "description" => "Office chair",
                "gross_total" => 150.75,
                "vat_code" => "19%",
                "purchase_category" => [
                    "id" => 101,
                    "name" => "Office Equipment"
                ]
            ]
        ],
        "refund_request" => null,
        "attachment_filename" => "receipt.pdf",
        "created_at" => "2023-12-01T10:00:00Z",
        "updated_at" => "2023-12-01T10:00:00Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "title" => "Office supplies",
            "date" => "2023-12-01",
            "currency" => "EUR",
            "items" => [
                [
                    "description" => "Office chair",
                    "gross_total" => 150.75,
                    "vat_code" => "19%",
                    "purchase_category_id" => 101
                ]
            ]
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $receipt = $this->mocoClient->receipts->create($params);
        $this->assertEquals(123, $receipt->id);
        $this->assertEquals("Office supplies", $receipt->title);
        $this->assertEquals(150.75, $receipt->gross_total);

        unset($params['title']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->receipts->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $receipts = $this->mocoClient->receipts->get();
        $this->assertEquals(123, $receipts[0]->id);
        $this->assertEquals("Office supplies", $receipts[0]->title);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $receipt = $this->mocoClient->receipts->get(123);
        $this->assertEquals("EUR", $receipt->currency);
        $this->assertTrue($receipt->billable);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->receipts->get(999);
    }

    public function testGetWithParams(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $receipts = $this->mocoClient->receipts->get(['project_id' => 789]);
        $this->assertEquals(123, $receipts[0]->id);

        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $receipts = $this->mocoClient->receipts->get([
            'date_from' => '2023-01-01',
            'date_to' => '2023-12-31'
        ]);
        $this->assertEquals(123, $receipts[0]->id);
    }

    public function testUpdate(): void
    {
        $this->expectedResponse['title'] = 'Updated Office supplies';
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $receipt = $this->mocoClient->receipts->update(123, ['title' => 'Updated Office supplies']);
        $this->assertEquals('Updated Office supplies', $receipt->title);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->receipts->delete(123));
    }

    public function testCreateWithAttachment(): void
    {
        $params = [
            "title" => "Receipt with attachment",
            "date" => "2023-12-01",
            "currency" => "EUR",
            "items" => [
                [
                    "description" => "Equipment",
                    "gross_total" => 250.00,
                    "vat_code" => "19%",
                    "purchase_category_id" => 101
                ]
            ],
            "attachment" => [
                "filename" => "receipt.pdf",
                "base64" => "JVBERi0xLjQKJcfsj6IKNSAwIG9iago8PC9MZW5ndGggNiAwIFI..."
            ]
        ];

        $responseWithAttachment = $this->expectedResponse;
        $responseWithAttachment['attachment_filename'] = 'receipt.pdf';

        $this->mockResponse(200, json_encode($responseWithAttachment));
        $receipt = $this->mocoClient->receipts->create($params);
        $this->assertEquals('receipt.pdf', $receipt->attachment_filename);
    }
}
