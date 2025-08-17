<?php

namespace Tests\Functional\Service;

use Functional\Service\Projects\ProjectsServiceTest;
use Moco\Entity\Comment;

class CommentsServiceTest extends AbstractServiceTest
{
    public function testCreate(): Comment
    {
        $projectsServiceTest = new ProjectsServiceTest();
        $project = $projectsServiceTest->testCreate();
        $params = [
            'commentable_id' => $project->id,
            'commentable_type' => 'Project',
            'text' => '<div>Project was ordered on <strong>1.10.2022</strong></div>.'
        ];

        $comment = $this->mocoClient->comments->create($params);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($project->id, $comment->commentable_id);
        return $comment;
    }

    /**
     * @depends testCreate
    */
    public function testGet(Comment $comment)
    {
        $comments = $this->mocoClient->comments->get();
        $this->assertIsArray($comments);

        $result = $this->mocoClient->comments->get($comment->id);
        $this->assertInstanceOf(Comment::class, $result);
        $this->assertEquals($result->id, $comment->id);

        $result = $this->mocoClient->comments->get(['commentable_type' => 'Project']);
        $this->assertIsArray($result);
        foreach ($result as $item) {
            $this->assertInstanceOf(Comment::class, $item);
        }
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(Comment $comment): Comment
    {
        $comment = $this->mocoClient->comments->update($comment->id, ['text' => 'updated']);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('updated', $comment->text);
        return $comment;
    }

    public function testBulkCreate(): void
    {
        $projectsServiceTest = new ProjectsServiceTest();
        $projectOne = $projectsServiceTest->testCreate();
        $projectTwo = $projectsServiceTest->testCreate();

        $params = [
            'commentable_ids' => [$projectOne->id, $projectTwo->id],
            'commentable_type' => 'Project',
            'text' => '<div>Project BULK</div>.'
        ];
        $comments = $this->mocoClient->comments->bulkCreate($params);
        $this->assertIsArray($comments);
        $this->assertCount(2, $comments);
        foreach ($comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
            $this->testDelete($comment);
        }
    }

    /**
     * @depends testUpdate
    */
    public function testDelete(Comment $comment): void
    {
        $this->assertNull($this->mocoClient->comments->delete($comment->id));
        $projectsServiceTest = new ProjectsServiceTest();
        $this->assertNull($projectsServiceTest->testDelete($comment->commentable_id));
    }
}
