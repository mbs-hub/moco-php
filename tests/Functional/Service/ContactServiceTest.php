<?php

declare(strict_types=1);

namespace Tests\Functional\Service;

use Moco\Entity\Contact;

class ContactServiceTest extends AbstractServiceTest
{
    private array $params = [
        'firstname' => 'Peter',
        'lastname' => 'Muster',
        'birthday' => '1959-05-22',
        'gender' => 'M'
    ];

    public function testCreate(): int
    {
        $contact = $this->mocoClient->contacts->create($this->params);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('Peter', $contact->firstname);
        return $contact->id;
    }

    /**
     * @depends testCreate
     */
    public function testGetPeople(): void
    {
        $contacts = $this->mocoClient->contacts->get();
        $this->assertIsArray($contacts);
    }

    /**
     * @depends testCreate
     */
    public function testGetContact(int $contactId): int
    {
        $contact = $this->mocoClient->contacts->get($contactId);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('Peter', $contact->firstname);
        return $contact->id;
    }

    /**
     * @depends testGetContact
     */
    public function testUpdate(int $contactId): int
    {
        $updateParams = ['firstname' => 'updated'];
        $contact = $this->mocoClient->contacts->update($contactId, $updateParams);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('updated', $contact->firstname);
        return $contact->id;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $contactId): void
    {
        $this->assertNull($this->mocoClient->contacts->delete($contactId));
    }
}
