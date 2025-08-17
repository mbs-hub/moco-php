<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use Moco\Entity\Profile;

class ProfileServiceTest extends AbstractServiceTest
{
    private array $expectedResponse = [
        "id" => 237852983,
        "email" => "janine.kuesters@meinefirma.de",
        "full_name" => "Janine Küsters",
        "first_name" => "Janine",
        "last_name" => "Küsters",
        "active" => true,
        "external" => false,
        "avatar_url" => "https://data.mocoapp.com/objects/6bf3db0a-895a-46a1-8006-6280af04b9c0.jpg",
        "unit" => [
            "id" => 436796,
            "name" => "Design"
        ],
        "created_at" => "2018-10-17T09:33:46Z",
        "updated_at" => "2022-08-02T14:21:56Z"
    ];

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->expectedResponse));
        $profile = $this->mocoClient->profile->get(null);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals(237852983, $profile->id);
        $this->assertEquals("janine.kuesters@meinefirma.de", $profile->email);
        $this->assertEquals("Janine Küsters", $profile->full_name);
        $this->assertEquals("Janine", $profile->first_name);
        $this->assertEquals("Küsters", $profile->last_name);
        $this->assertTrue($profile->active);
        $this->assertFalse($profile->external);
        $this->assertEquals("https://data.mocoapp.com/objects/6bf3db0a-895a-46a1-8006-6280af04b9c0.jpg", $profile->avatar_url);
        $this->assertEquals(436796, $profile->unit->id);
        $this->assertEquals("Design", $profile->unit->name);
        $this->assertEquals("2018-10-17T09:33:46Z", $profile->created_at);
        $this->assertEquals("2022-08-02T14:21:56Z", $profile->updated_at);
    }
}
