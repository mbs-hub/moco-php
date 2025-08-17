<?php

namespace Functional\Service\Purchases;

use Moco\Entity\PurchaseDraft;
use Tests\Functional\Service\AbstractServiceTest;

class PurchaseDraftsServiceTest extends AbstractServiceTest
{
    public function testGet(): void
    {
        $drafts = $this->mocoClient->purchaseDrafts->get();
        $this->assertIsArray($drafts);

        if (count($drafts) > 0) {
            $this->assertInstanceOf(PurchaseDraft::class, $drafts[0]);
            $this->assertIsInt($drafts[0]->id);
            $this->assertIsString($drafts[0]->title);

            // Test getting single draft
            $singleDraft = $this->mocoClient->purchaseDrafts->get($drafts[0]->id);
            $this->assertInstanceOf(PurchaseDraft::class, $singleDraft);
            $this->assertEquals($drafts[0]->id, $singleDraft->id);
        }
    }

    public function testGetWithParams(): void
    {
        $drafts = $this->mocoClient->purchaseDrafts->get(['limit' => 5]);
        $this->assertIsArray($drafts);
        $this->assertLessThanOrEqual(5, count($drafts));

        foreach ($drafts as $draft) {
            $this->assertInstanceOf(PurchaseDraft::class, $draft);
        }
    }

    public function testGetPdf(): void
    {
        $drafts = $this->mocoClient->purchaseDrafts->get();

        if (count($drafts) > 0) {
            $draftId = $drafts[0]->id;

            // Test PDF retrieval - might return content or empty string if no PDF
            $pdfResult = $this->mocoClient->purchaseDrafts->getPdf($draftId);
            $this->assertIsString($pdfResult);
        } else {
            $this->markTestSkipped('No purchase drafts available for PDF testing');
        }
    }
}
