<?php

namespace Tests\Functional\Service;

use Moco\Entity\WebHook;

class WebHooksServiceTest extends AbstractServiceTest
{
    public function testGetAllWebHooks(): void
    {
        $webHooks = $this->mocoClient->webHooks->get();

        $this->assertIsArray($webHooks);

        if (!empty($webHooks)) {
            $this->assertInstanceOf(WebHook::class, $webHooks[0]);
            $this->assertIsInt($webHooks[0]->id);
            $this->assertIsString($webHooks[0]->target);
            $this->assertIsString($webHooks[0]->event);
            $this->assertIsString($webHooks[0]->hook);
            $this->assertIsBool($webHooks[0]->disabled);
            $this->assertIsString($webHooks[0]->created_at);
            $this->assertIsString($webHooks[0]->updated_at);
        }
    }

    public function testGetSingleWebHook(): void
    {
        // First get all webhooks to get a valid ID
        $webHooks = $this->mocoClient->webHooks->get();

        if (!empty($webHooks)) {
            $webHookId = $webHooks[0]->id;
            $webHook = $this->mocoClient->webHooks->get($webHookId);

            $this->assertInstanceOf(WebHook::class, $webHook);
            $this->assertEquals($webHookId, $webHook->id);
            $this->assertIsString($webHook->target);
            $this->assertIsString($webHook->event);
            $this->assertIsString($webHook->hook);
            $this->assertIsBool($webHook->disabled);
        } else {
            $this->markTestSkipped('No webhooks available to test with');
        }
    }

    public function testCreateWebHook(): void
    {
        $webHookData = [
            'target' => WebHook::TARGET_ACTIVITY,
            'event' => WebHook::EVENT_CREATE,
            'hook' => 'https://test.example.org/webhook-' . time()
        ];

        $webHook = $this->mocoClient->webHooks->create($webHookData);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals($webHookData['target'], $webHook->target);
        $this->assertEquals($webHookData['event'], $webHook->event);
        $this->assertEquals($webHookData['hook'], $webHook->hook);
        $this->assertIsInt($webHook->id);
        $this->assertIsBool($webHook->disabled);

        // Store the ID for cleanup
        $createdWebHookId = $webHook->id;

        // Clean up - delete the created webhook
        $this->mocoClient->webHooks->delete($createdWebHookId);
    }

    public function testCreateWebHookWithDifferentTargets(): void
    {
        $targets = [
            WebHook::TARGET_COMPANY,
            WebHook::TARGET_CONTACT,
            WebHook::TARGET_PROJECT
        ];

        $createdIds = [];

        foreach ($targets as $target) {
            $webHookData = [
                'target' => $target,
                'event' => WebHook::EVENT_UPDATE,
                'hook' => 'https://test.example.org/' . strtolower($target) . '-' . time()
            ];

            $webHook = $this->mocoClient->webHooks->create($webHookData);

            $this->assertInstanceOf(WebHook::class, $webHook);
            $this->assertEquals($target, $webHook->target);
            $this->assertEquals(WebHook::EVENT_UPDATE, $webHook->event);

            $createdIds[] = $webHook->id;
        }

        // Clean up all created webhooks
        foreach ($createdIds as $id) {
            $this->mocoClient->webHooks->delete($id);
        }
    }

    public function testUpdateWebHook(): void
    {
        // First create a webhook to update
        $webHookData = [
            'target' => WebHook::TARGET_ACTIVITY,
            'event' => WebHook::EVENT_CREATE,
            'hook' => 'https://test.example.org/original-' . time()
        ];
        $webHook = $this->mocoClient->webHooks->create($webHookData);
        $webHookId = $webHook->id;

        // Update the webhook
        $updateData = [
            'hook' => 'https://test.example.org/updated-' . time()
        ];
        $updatedWebHook = $this->mocoClient->webHooks->update($webHookId, $updateData);

        $this->assertInstanceOf(WebHook::class, $updatedWebHook);
        $this->assertEquals($updateData['hook'], $updatedWebHook->hook);
        $this->assertEquals($webHookId, $updatedWebHook->id);

        // Clean up
        $this->mocoClient->webHooks->delete($webHookId);
    }

    public function testEnableDisableWebHook(): void
    {
        // First create a webhook
        $webHookData = [
            'target' => WebHook::TARGET_COMPANY,
            'event' => WebHook::EVENT_UPDATE,
            'hook' => 'https://test.example.org/toggle-' . time()
        ];
        $webHook = $this->mocoClient->webHooks->create($webHookData);
        $webHookId = $webHook->id;

        // Test disabling the webhook
        $disabledWebHook = $this->mocoClient->webHooks->disable($webHookId);
        $this->assertInstanceOf(WebHook::class, $disabledWebHook);
        $this->assertTrue($disabledWebHook->disabled);
        $this->assertEquals($webHookId, $disabledWebHook->id);

        // Test enabling the webhook
        $enabledWebHook = $this->mocoClient->webHooks->enable($webHookId);
        $this->assertInstanceOf(WebHook::class, $enabledWebHook);
        $this->assertFalse($enabledWebHook->disabled);
        $this->assertEquals($webHookId, $enabledWebHook->id);

        // Clean up
        $this->mocoClient->webHooks->delete($webHookId);
    }

    public function testDeleteWebHook(): void
    {
        // Create a webhook to delete
        $webHookData = [
            'target' => WebHook::TARGET_CONTACT,
            'event' => WebHook::EVENT_DELETE,
            'hook' => 'https://test.example.org/delete-' . time()
        ];
        $webHook = $this->mocoClient->webHooks->create($webHookData);
        $webHookId = $webHook->id;

        // Delete the webhook
        $result = $this->mocoClient->webHooks->delete($webHookId);

        $this->assertNull($result);

        // Verify the webhook was deleted by trying to get it (should throw exception)
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->webHooks->get($webHookId);
    }

    public function testWebHookFiltering(): void
    {
        // Create webhooks with different targets for filtering
        $webHookData1 = [
            'target' => WebHook::TARGET_ACTIVITY,
            'event' => WebHook::EVENT_CREATE,
            'hook' => 'https://test.example.org/filter1-' . time()
        ];
        $webHookData2 = [
            'target' => WebHook::TARGET_COMPANY,
            'event' => WebHook::EVENT_UPDATE,
            'hook' => 'https://test.example.org/filter2-' . time()
        ];

        $webHook1 = $this->mocoClient->webHooks->create($webHookData1);
        $webHook2 = $this->mocoClient->webHooks->create($webHookData2);

        // Test filtering by target (if supported by API)
        $allWebHooks = $this->mocoClient->webHooks->get();
        $this->assertIsArray($allWebHooks);

        // Clean up
        $this->mocoClient->webHooks->delete($webHook1->id);
        $this->mocoClient->webHooks->delete($webHook2->id);
    }

    public function testCompleteWorkflow(): void
    {
        $hookUrl = 'https://test.example.org/workflow-' . time();

        // 1. Create a new webhook
        $createData = [
            'target' => WebHook::TARGET_PROJECT,
            'event' => WebHook::EVENT_CREATE,
            'hook' => $hookUrl
        ];
        $createdWebHook = $this->mocoClient->webHooks->create($createData);

        $this->assertInstanceOf(WebHook::class, $createdWebHook);
        $this->assertEquals(WebHook::TARGET_PROJECT, $createdWebHook->target);
        $this->assertEquals(WebHook::EVENT_CREATE, $createdWebHook->event);
        $this->assertEquals($hookUrl, $createdWebHook->hook);
        $webHookId = $createdWebHook->id;

        // 2. Get the created webhook
        $fetchedWebHook = $this->mocoClient->webHooks->get($webHookId);
        $this->assertEquals($webHookId, $fetchedWebHook->id);
        $this->assertEquals($hookUrl, $fetchedWebHook->hook);

        // 3. Update the webhook
        $updatedUrl = 'https://test.example.org/updated-workflow-' . time();
        $updatedWebHook = $this->mocoClient->webHooks->update($webHookId, ['hook' => $updatedUrl]);
        $this->assertEquals($updatedUrl, $updatedWebHook->hook);

        // 4. Disable the webhook
        $disabledWebHook = $this->mocoClient->webHooks->disable($webHookId);
        $this->assertTrue($disabledWebHook->disabled);

        // 5. Enable the webhook
        $enabledWebHook = $this->mocoClient->webHooks->enable($webHookId);
        $this->assertFalse($enabledWebHook->disabled);

        // 6. Delete the webhook
        $this->mocoClient->webHooks->delete($webHookId);

        // 7. Verify deletion (should throw NotFoundException)
        $this->expectException(\Moco\Exception\NotFoundException::class);
        $this->mocoClient->webHooks->get($webHookId);
    }

    public function testWebHookEntityProperties(): void
    {
        $webHooks = $this->mocoClient->webHooks->get();

        if (!empty($webHooks)) {
            $webHook = $webHooks[0];

            // Test that all expected properties exist
            $this->assertTrue(property_exists($webHook, 'id'));
            $this->assertTrue(property_exists($webHook, 'target'));
            $this->assertTrue(property_exists($webHook, 'event'));
            $this->assertTrue(property_exists($webHook, 'hook'));
            $this->assertTrue(property_exists($webHook, 'disabled'));
            $this->assertTrue(property_exists($webHook, 'created_at'));
            $this->assertTrue(property_exists($webHook, 'updated_at'));

            // Test mandatory fields method
            $this->assertEquals(['target', 'event', 'hook'], $webHook->getMandatoryFields());
        }
    }

    public function testCreateWebHookWithAllEvents(): void
    {
        $events = [
            WebHook::EVENT_CREATE,
            WebHook::EVENT_UPDATE,
            WebHook::EVENT_DELETE
        ];

        $createdIds = [];

        foreach ($events as $event) {
            $webHookData = [
                'target' => WebHook::TARGET_ACTIVITY,
                'event' => $event,
                'hook' => 'https://test.example.org/' . $event . '-' . time()
            ];

            $webHook = $this->mocoClient->webHooks->create($webHookData);

            $this->assertInstanceOf(WebHook::class, $webHook);
            $this->assertEquals(WebHook::TARGET_ACTIVITY, $webHook->target);
            $this->assertEquals($event, $webHook->event);

            $createdIds[] = $webHook->id;
        }

        // Clean up all created webhooks
        foreach ($createdIds as $id) {
            $this->mocoClient->webHooks->delete($id);
        }
    }
}
