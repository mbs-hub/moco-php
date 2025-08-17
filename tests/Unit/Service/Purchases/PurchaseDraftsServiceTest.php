<?php

namespace Tests\Unit\Service\Purchases;

use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class PurchaseDraftsServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 123,
        "title" => "Ticket",
        "email_from" => "john@example.com",
        "email_body" => "here the ticket",
        "user" => [
            "id" => 933590696,
            "firstname" => "John",
            "lastname" => "Doe"
        ],
        "file_url" => "https://example.com/file.pdf",
        "created_at" => "2021-10-17T09:33:46Z",
        "updated_at" => "2021-10-17T09:33:46Z"
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $drafts = $this->mocoClient->purchaseDrafts->get();
        $this->assertEquals(123, $drafts[0]->id);
        $this->assertEquals("Ticket", $drafts[0]->title);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $draft = $this->mocoClient->purchaseDrafts->get(123);
        $this->assertEquals("john@example.com", $draft->email_from);
        $this->assertEquals("here the ticket", $draft->email_body);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->purchaseDrafts->get(999);
    }

    public function testGetWithParams(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $drafts = $this->mocoClient->purchaseDrafts->get(['title' => 'Ticket']);
        $this->assertEquals(123, $drafts[0]->id);
    }

    public function testGetPdf(): void
    {
        $pdfContent = '%PDF-1.4 dummy content';
        $this->mockResponse(200, $pdfContent);
        $result = $this->mocoClient->purchaseDrafts->getPdf(123);
        $this->assertEquals($pdfContent, $result);

        $this->mockResponse(204, '');
        $result = $this->mocoClient->purchaseDrafts->getPdf(123);
        $this->assertEquals('', $result);
    }
}
