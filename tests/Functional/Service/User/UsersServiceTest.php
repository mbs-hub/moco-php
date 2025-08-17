<?php

namespace Functional\Service\User;

use Moco\Exception\InvalidRequestException;
use Tests\Functional\Service\AbstractServiceTest;

class UsersServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'firstname'    => 'ft_firstname',
        'lastname'     => 'ft_lastname',
        'email'        => 'mehdi.bagheri@ft.de',
        'password'     => 'ft_password',
        'unit_id'      => 909187636,
        'active'       => true,
        'language'     => 'de',
        'mobile_phone' => '+41 79 123 45 67',
        'work_phone'   => '+41 44 123 45 67',
        'home_address' => "Peter Müller\nBeispielstrasse 123\nBeispielstadt",
        'bday'         => '1975-01-17',
        'tags'         => ["Deutschland"],
        'info'         => 'info',
    ];

    public function testCreate(): int
    {
        /**
* if it was successful then the user is object is returned so
*/
        $this->createParams['email'] = 'mehdi.bagheri' . time() . '@ft.de';
        $user = $this->mocoClient->users->create($this->createParams);
        $this->assertEquals($user->firstname, 'ft_firstname');
        return $user->id;
    }

    public function testCreateInvalidRequestException()
    {
        unset($this->createParams['firstname']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->users->create($this->createParams);
    }

    /**
     * @depends testCreate
     */
    public function testCreateInvalidResponseException()
    {
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->users->create($this->createParams);
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $userId): int
    {
        $user = $this->mocoClient->users->get($userId);
        $this->assertEquals('ft_lastname', $user->lastname);

        $users = $this->mocoClient->users->get();
        $this->assertIsArray($users);
        return $userId;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $userId): int
    {
        $updateParams = ['firstname' => 'ft_updated'];
        $user = $this->mocoClient->users->update($userId, $updateParams);
        $this->assertEquals('ft_updated', $user->firstname);
        return $userId;
    }

    /**
     * @depends testGet
     */
    public function testGetPerformanceReport(int $userId)
    {
        $datetime = new \DateTime();
        $result = $this->mocoClient->users->getPerformanceReport($userId, ['year' => $datetime->format('Y')]);
        $this->assertEquals((int) $datetime->format('Y'), $result->annually->year);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $userId)
    {
        $this->assertNull($this->mocoClient->users->delete($userId));
    }
}
