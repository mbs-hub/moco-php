<?php

namespace Tests\Unit\Service;

use Moco\Entity\Comment;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\NotFoundException;

class CommentsServiceTest extends AbstractServiceTest
{
    private array $mockedResult = [
        "id"               => 123,
        "commentable_id"   => 12345,
        "commentable_type" => "Project",
        "text"             => "<div>Project was ordered on <strong>1.10.2017</strong></div>.",
        "manual"           => true,
        "user"             => [
            "id"        => 567,
            "firstname" => "Tobias",
            "lastname"  => "Miesel",
        ],
        "created_at"       => "2018-10-17T09:33:46Z",
        "updated_at"       => "2018-10-17T09:33:46Z",
    ];

    public function testCreate(): void
    {
        $params = [
            'commentable_id' => 123,
            'commentable_type' => 'Project',
            'text' => '<div>Project was ordered on <strong>1.10.2022</strong></div>.'
        ];
        $this->mockResponse(200, json_encode($this->mockedResult));
        $comment = $this->mocoClient->comments->create($params);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('Project', $comment->commentable_type);

        $this->expectException(InvalidRequestException::class);
        unset($params['commentable_type']);
        $this->mocoClient->comments->create($params);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode([$this->mockedResult]));
        $comments = $this->mocoClient->comments->get();
        $this->assertIsArray($comments);
        $this->assertEquals('Project', $comments[0]->commentable_type);

        $this->mockResponse(200, json_encode($this->mockedResult));
        $comment = $this->mocoClient->comments->get(123);
        $this->assertInstanceOf(Comment::class, $comment);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->comments->get(123456);
    }

    public function testUpdate(): void
    {
        $mockedResult = $this->mockedResult;
        $mockedResult['text'] = 'updated';
        $this->mockResponse(200, json_encode($mockedResult));
        $comment = $this->mocoClient->comments->update(123, ['text' => 'updated']);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals('updated', $comment->text);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->comments->delete(123));
    }

    public function testBulkCreate(): void
    {
        $params = [
            'commentable_ids' => [12345, 123456],
            'commentable_type' => 'Project',
            'text' => '<div>Project BULK</div>.'
        ];

        $mockedResult[0] = $this->mockedResult;
        $mockedResult[1] = $this->mockedResult;
        $mockedResult[1]['commentable_id'] = 123456;
        $mockedResult[1]['id'] = 1234;
        $this->mockResponse(200, json_encode($mockedResult));
        $comments = $this->mocoClient->comments->bulkCreate($params);
        $this->assertIsArray($comments);
        foreach ($comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
        }
    }
}
