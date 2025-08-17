<?php

declare(strict_types=1);

namespace Tests\Functional\Service;

use Moco\Entity\Profile;

class ProfileServiceTest extends AbstractServiceTest
{
    public function testGet(): int
    {
        $profile = $this->mocoClient->profile->get(null);
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertNotNull($profile->id);
        $this->assertNotEmpty($profile->email);
        $this->assertNotEmpty($profile->full_name);
        $this->assertNotEmpty($profile->first_name);
        $this->assertNotEmpty($profile->last_name);
        $this->assertIsBool($profile->active);
        $this->assertIsBool($profile->external);
        $this->assertNotNull($profile->unit);
        $this->assertNotNull($profile->unit->id);
        $this->assertNotEmpty($profile->unit->name);
        $this->assertNotNull($profile->created_at);
        $this->assertNotNull($profile->updated_at);

        return $profile->id;
    }
}
