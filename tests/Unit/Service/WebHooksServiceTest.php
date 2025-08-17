<?php

namespace Tests\Unit\Service;

use Moco\Entity\WebHook;

class WebHooksServiceTest extends AbstractServiceTest
{
    private array $expectedWebHook = [
        'id' => 123456,
        'target' => 'Activity',
        'event' => 'create',
        'hook' => 'https://example.org/do-stuff',
        'disabled' => false,
        'created_at' => '2018-10-17T09:33:46Z',
        'updated_at' => '2018-10-17T09:33:46Z'
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedWebHook));
        $webHook = $this->mocoClient->webHooks->get(123456);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals($this->expectedWebHook['id'], $webHook->id);
        $this->assertEquals($this->expectedWebHook['target'], $webHook->target);
        $this->assertEquals($this->expectedWebHook['event'], $webHook->event);
        $this->assertEquals($this->expectedWebHook['hook'], $webHook->hook);
        $this->assertIsBool($webHook->disabled);
    }

    public function testGetAll(): void
    {
        $expectedWebHooks = [$this->expectedWebHook];
        $this->mockResponse(200, json_encode($expectedWebHooks));
        $webHooks = $this->mocoClient->webHooks->get();

        $this->assertIsArray($webHooks);
        $this->assertCount(1, $webHooks);
        $this->assertInstanceOf(WebHook::class, $webHooks[0]);
        $this->assertEquals($this->expectedWebHook['target'], $webHooks[0]->target);
        $this->assertEquals($this->expectedWebHook['event'], $webHooks[0]->event);
    }

    public function testGetWithFilters(): void
    {
        $filters = ['target' => 'Activity'];
        $this->mockResponse(200, json_encode([$this->expectedWebHook]));
        $webHooks = $this->mocoClient->webHooks->get($filters);

        $this->assertIsArray($webHooks);
        $this->assertCount(1, $webHooks);
        $this->assertInstanceOf(WebHook::class, $webHooks[0]);
        $this->assertEquals('Activity', $webHooks[0]->target);
    }

    public function testGetEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $webHooks = $this->mocoClient->webHooks->get();

        $this->assertIsArray($webHooks);
        $this->assertEmpty($webHooks);
    }

    public function testCreate(): void
    {
        $params = [
            'target' => 'Activity',
            'event' => 'create',
            'hook' => 'https://example.org/webhook'
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedWebHook, $params)));
        $webHook = $this->mocoClient->webHooks->create($params);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals('Activity', $webHook->target);
        $this->assertEquals('create', $webHook->event);
        $this->assertEquals('https://example.org/webhook', $webHook->hook);
    }

    public function testCreateWithAllMandatoryFields(): void
    {
        $params = [
            'target' => 'Company',
            'event' => 'update',
            'hook' => 'https://example.org/company-webhook'
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedWebHook, $params)));
        $webHook = $this->mocoClient->webHooks->create($params);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals('Company', $webHook->target);
        $this->assertEquals('update', $webHook->event);
        $this->assertEquals('https://example.org/company-webhook', $webHook->hook);
    }

    public function testUpdate(): void
    {
        $webHookId = 123456;
        $params = ['hook' => 'https://updated.example.org/webhook'];
        $updatedWebHook = array_merge($this->expectedWebHook, $params);

        $this->mockResponse(200, json_encode($updatedWebHook));
        $webHook = $this->mocoClient->webHooks->update($webHookId, $params);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals('https://updated.example.org/webhook', $webHook->hook);
        $this->assertEquals($webHookId, $webHook->id);
    }

    public function testUpdatePartial(): void
    {
        $webHookId = 123456;
        $params = ['target' => 'Project'];
        $updatedWebHook = array_merge($this->expectedWebHook, $params);

        $this->mockResponse(200, json_encode($updatedWebHook));
        $webHook = $this->mocoClient->webHooks->update($webHookId, $params);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals('Project', $webHook->target);
    }

    public function testDelete(): void
    {
        $webHookId = 123456;
        $this->mockResponse(204, '');
        $result = $this->mocoClient->webHooks->delete($webHookId);

        $this->assertNull($result);
    }

    public function testEnable(): void
    {
        $webHookId = 123456;
        $enabledWebHook = array_merge($this->expectedWebHook, ['disabled' => false]);
        $this->mockResponse(200, json_encode($enabledWebHook));
        $webHook = $this->mocoClient->webHooks->enable($webHookId);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertFalse($webHook->disabled);
        $this->assertEquals($webHookId, $webHook->id);
    }

    public function testDisable(): void
    {
        $webHookId = 123456;
        $disabledWebHook = array_merge($this->expectedWebHook, ['disabled' => true]);
        $this->mockResponse(200, json_encode($disabledWebHook));
        $webHook = $this->mocoClient->webHooks->disable($webHookId);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertTrue($webHook->disabled);
        $this->assertEquals($webHookId, $webHook->id);
    }

    public function testEntityMandatoryFields(): void
    {
        $webHook = new WebHook();
        $mandatoryFields = $webHook->getMandatoryFields();

        $this->assertEquals(['target', 'event', 'hook'], $mandatoryFields);
    }

    public function testServiceEndpoint(): void
    {
        $reflection = new \ReflectionClass($this->mocoClient->webHooks);
        $method = $reflection->getMethod('getEndpoint');
        $method->setAccessible(true);
        $endpoint = $method->invoke($this->mocoClient->webHooks);

        $this->assertStringContainsString('account/web_hooks', $endpoint);
    }

    public function testWebHookProperties(): void
    {
        $this->mockResponse(200, json_encode($this->expectedWebHook));
        $webHook = $this->mocoClient->webHooks->get(123456);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertIsInt($webHook->id);
        $this->assertIsString($webHook->target);
        $this->assertIsString($webHook->event);
        $this->assertIsString($webHook->hook);
        $this->assertIsBool($webHook->disabled);
        $this->assertIsString($webHook->created_at);
        $this->assertIsString($webHook->updated_at);
    }

    public function testWebHookConstants(): void
    {
        // Test target constants
        $this->assertEquals('Activity', WebHook::TARGET_ACTIVITY);
        $this->assertEquals('Company', WebHook::TARGET_COMPANY);
        $this->assertEquals('Contact', WebHook::TARGET_CONTACT);
        $this->assertEquals('Project', WebHook::TARGET_PROJECT);
        $this->assertEquals('Invoice', WebHook::TARGET_INVOICE);
        $this->assertEquals('Offer', WebHook::TARGET_OFFER);
        $this->assertEquals('Deal', WebHook::TARGET_DEAL);
        $this->assertEquals('Expense', WebHook::TARGET_EXPENSE);

        // Test event constants
        $this->assertEquals('create', WebHook::EVENT_CREATE);
        $this->assertEquals('update', WebHook::EVENT_UPDATE);
        $this->assertEquals('delete', WebHook::EVENT_DELETE);
    }

    public function testCreateWithConstants(): void
    {
        $params = [
            'target' => WebHook::TARGET_ACTIVITY,
            'event' => WebHook::EVENT_CREATE,
            'hook' => 'https://example.org/webhook'
        ];
        $this->mockResponse(200, json_encode(array_merge($this->expectedWebHook, $params)));
        $webHook = $this->mocoClient->webHooks->create($params);

        $this->assertInstanceOf(WebHook::class, $webHook);
        $this->assertEquals(WebHook::TARGET_ACTIVITY, $webHook->target);
        $this->assertEquals(WebHook::EVENT_CREATE, $webHook->event);
    }
}
