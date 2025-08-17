<?php

namespace Tests\Unit\Service;

use Moco\Exception\InvalidRequestException;

class TagsServiceTest extends AbstractServiceTest
{
    private array $expectedTags = ["cool", "on hold", "important"];

    public function testGetTags(): void
    {
        $this->mockResponse(200, json_encode($this->expectedTags));
        $tags = $this->mocoClient->tags->getTags('Project', 123);

        $this->assertIsArray($tags);
        $this->assertEquals(['cool', 'on hold', 'important'], $tags);
    }

    public function testGetTagsEmpty(): void
    {
        $this->mockResponse(200, json_encode([]));
        $tags = $this->mocoClient->tags->getTags('Company', 456);

        $this->assertIsArray($tags);
        $this->assertEmpty($tags);
    }

    public function testAddTags(): void
    {
        $newTags = ['new-tag', 'another-tag'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->addTags('Contact', 789, $newTags);

        $this->assertNull($result);
    }

    public function testReplaceTags(): void
    {
        $newTags = ['replaced-tag', 'only-tag'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->replaceTags('Deal', 101, $newTags);

        $this->assertNull($result);
    }

    public function testReplaceTagsEmpty(): void
    {
        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->replaceTags('Purchase', 202, []);

        $this->assertNull($result);
    }

    public function testRemoveTags(): void
    {
        $tagsToRemove = ['on hold'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->removeTags('Invoice', 303, $tagsToRemove);

        $this->assertNull($result);
    }

    public function testRemoveMultipleTags(): void
    {
        $tagsToRemove = ['cool', 'on hold'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->removeTags('Offer', 404, $tagsToRemove);

        $this->assertNull($result);
    }

    public function testSupportedEntities(): void
    {
        $supportedEntities = ['Company', 'Contact', 'Project', 'Deal', 'Purchase', 'Invoice', 'Offer', 'User'];

        foreach ($supportedEntities as $entity) {
            $this->mockResponse(200, json_encode($this->expectedTags));
            $tags = $this->mocoClient->tags->getTags($entity, 123);
            $this->assertIsArray($tags);
        }
    }

    public function testInvalidEntityValidation(): void
    {
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('Invalid entity type');

        $this->mocoClient->tags->getTags('invalid-entity', 123);
    }

    public function testInvalidEntityValidationForAddTags(): void
    {
        $this->expectException(InvalidRequestException::class);

        $this->mocoClient->tags->addTags('invalid-entity', 123, ['tag']);
    }

    public function testInvalidEntityValidationForReplaceTags(): void
    {
        $this->expectException(InvalidRequestException::class);

        $this->mocoClient->tags->replaceTags('invalid-entity', 123, ['tag']);
    }

    public function testInvalidEntityValidationForRemoveTags(): void
    {
        $this->expectException(InvalidRequestException::class);

        $this->mocoClient->tags->removeTags('invalid-entity', 123, ['tag']);
    }

    public function testAddTagsIdempotent(): void
    {
        // Adding existing tags should be idempotent
        $existingTag = ['cool']; // Already exists in expectedTags

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->addTags('User', 505, $existingTag);

        $this->assertNull($result);
    }

    public function testRemoveNonExistentTags(): void
    {
        // Removing non-existent tags should be gracefully handled
        $nonExistentTags = ['non-existent-tag'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->removeTags('Project', 606, $nonExistentTags);

        $this->assertNull($result);
    }

    public function testTagsWithSpecialCharacters(): void
    {
        $specialTags = ['tag-with-dashes', 'tag_with_underscores', 'tag with spaces'];

        $this->mockResponse(200, '');
        $result = $this->mocoClient->tags->addTags('Company', 707, $specialTags);

        $this->assertNull($result);
    }
}
