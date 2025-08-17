<?php

namespace Functional\Service;

use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class TagsServiceTest extends AbstractServiceTest
{
    private int $testProjectId = 947556942;

    public function testGetTags(): void
    {
        $tags = $this->mocoClient->tags->getTags('Project', $this->testProjectId);
        $this->assertIsArray($tags);

        foreach ($tags as $tag) {
            $this->assertIsString($tag);
        }
    }

    public function testAddTags(): void
    {
        $testTags = ['functional-test-tag', 'automated-tag-' . time()];
        $this->mocoClient->tags->addTags('Project', $this->testProjectId, $testTags);
        $this->assertTrue(true); // Just verify no exception was thrown

        // Clean up
        $this->mocoClient->tags->removeTags('Project', $this->testProjectId, $testTags);
    }

    public function testRemoveTags(): void
    {
        $testTags = ['remove-test-tag-' . time()];

        // Add tags first
        $this->mocoClient->tags->addTags('Project', $this->testProjectId, $testTags);

        // Now remove them
        $this->mocoClient->tags->removeTags('Project', $this->testProjectId, $testTags);
        $this->assertTrue(true); // Just verify no exception was thrown
    }

    public function testReplaceTags(): void
    {
        // First, get current tags to restore later
        $originalTags = $this->mocoClient->tags->getTags('Project', $this->testProjectId);

        $newTags = ['replacement-tag-' . time(), 'another-replacement-tag'];

        $this->assertNull($this->mocoClient->tags->replaceTags('Project', $this->testProjectId, $newTags));
    }

    public function testClearAllTags(): void
    {
        // First, get current tags to restore later
        $originalTags = $this->mocoClient->tags->getTags('Project', $this->testProjectId);

        // Clear all tags
        $this->assertNull($this->mocoClient->tags->replaceTags('Project', $this->testProjectId, []));
    }

    public function testSupportedEntities(): void
    {
        $supportedEntities = [
            'Project' => $this->testProjectId,
            'Company' => 762611472,
            'User' => 933736920
        ];

        foreach ($supportedEntities as $entity => $entityId) {
            $tags = $this->mocoClient->tags->getTags($entity, $entityId);
            $this->assertIsArray($tags);

            foreach ($tags as $tag) {
                $this->assertIsString($tag);
            }
        }
    }

    public function testInvalidEntity(): void
    {
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->tags->getTags('invalid-entity', 123);
    }

    public function testRemoveNonExistentTag(): void
    {
        $nonExistentTag = 'definitely-does-not-exist-' . time();
        $this->assertNull($this->mocoClient->tags->removeTags('Project', $this->testProjectId, [$nonExistentTag]));
    }
}
