<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Offer;

use Moco\Entity\Offer;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;
use Tests\Unit\Service\AbstractServiceTest;

class OffersServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 12345,
        "identifier" => "A1907-042",
        "date" => "2019-06-24",
        "due_date" => "2019-07-08",
        "title" => "Offer for Website Development",
        "recipient_address" => "Example Company\nExample Street 123\n12345 Example City",
        "currency" => "EUR",
        "net_total" => 5000.00,
        "tax" => 19.0,
        "gross_total" => 5950.00,
        "discount" => "5%",
        "status" => "created",
        "tags" => ["Web", "Development"],
        "custom_properties" => [
            "Project Type" => "Website"
        ],
        "company" => [
            "id" => 123,
            "name" => "Example Company"
        ],
        "project" => [
            "id" => 456,
            "name" => "Website Project"
        ],
        "items" => [
            [
                "id" => 1,
                "type" => "item",
                "title" => "Website Development",
                "description" => "Frontend and backend development",
                "quantity" => 40.0,
                "unit" => "h",
                "unit_price" => 125.0,
                "net_total" => 5000.0
            ]
        ],
        "notes" => "Notes for this offer",
        "created_at" => "2019-06-24T09:33:46Z",
        "updated_at" => "2019-06-24T09:33:46Z"
    ];

    public function testCreate(): void
    {
        $params = [
            "recipient_address" => "Example Company\nExample Street 123\n12345 Example City",
            "date" => "2019-06-24",
            "due_date" => "2019-07-08",
            "title" => "Offer for Website Development",
            "tax" => 19.0,
            "items" => [
                [
                    "type" => "item",
                    "title" => "Website Development",
                    "quantity" => 40.0,
                    "unit" => "h",
                    "unit_price" => 125.0,
                    "net_total" => 5000.0
                ]
            ]
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $offer = $this->mocoClient->offers->create($params);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals(12345, $offer->id);
        $this->assertEquals("Offer for Website Development", $offer->title);

        unset($params['recipient_address']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->offers->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->expectedResponse]));
        $offers = $this->mocoClient->offers->get();
        $this->assertIsArray($offers);
        $this->assertEquals(12345, $offers[0]->id);

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $offer = $this->mocoClient->offers->get(12345);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals("A1907-042", $offer->identifier);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->offers->get(999999);
    }

    public function testGetPdf(): void
    {
        $pdfContent = "%PDF-1.4 sample pdf content";
        $this->mockResponse(200, $pdfContent);
        $result = $this->mocoClient->offers->getPdf(12345);
        $this->assertEquals($pdfContent, $result);
    }

    public function testAssign(): void
    {
        $assignParams = [
            "company_id" => 123,
            "project_id" => 456
        ];

        $this->mockResponse(200, json_encode($this->expectedResponse));
        $offer = $this->mocoClient->offers->assign(12345, $assignParams);
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals(12345, $offer->id);
    }

    public function testUpdateStatus(): void
    {
        $this->mockResponse(200, '');
        $this->assertNull($this->mocoClient->offers->updateStatus(12345, 'sent'));
    }

    public function testSendEmail(): void
    {
        $emailParams = [
            "subject" => "Offer A1907-042 for Website Development",
            "text" => "Dear customer, please find attached our offer for your website development project.",
            "emails_to" => "client@example.com"
        ];

        $this->mockResponse(200, json_encode($emailParams));
        $email = $this->mocoClient->offers->sendEmail(12345, $emailParams);
        $this->assertEquals($emailParams['text'], $email->text);

        unset($emailParams['subject']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->offers->sendEmail(12345, $emailParams);
    }

    public function testGetAttachments(): void
    {
        $attachmentsData = [
            [
                "id" => 101,
                "filename" => "specifications.pdf",
                "size" => 1024
            ]
        ];

        $this->mockResponse(200, json_encode($attachmentsData));
        $attachments = $this->mocoClient->offers->getAttachments(12345);
        $this->assertIsArray($attachments);
        $this->assertEquals(101, $attachments[0]->id);
    }

    public function testCreateAttachment(): void
    {
        $attachmentData = [
            "id" => 102,
            "filename" => "contract.pdf",
            "size" => 2048
        ];

        $this->mockResponse(200, json_encode($attachmentData));
        $attachment = $this->mocoClient->offers->createAttachment(12345, [
            'filename' => 'contract.pdf',
            'file' => 'base64encodedcontent'
        ]);
        $this->assertEquals(102, $attachment->id);
    }

    public function testDeleteAttachment(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->offers->deleteAttachment(12345, 102));
    }
}
