<?php

namespace Tests\Unit\Service;

use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;

class CompaniesServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'name'                            => 'Company A',
        'type'                            => 'customer',
        'country_code'                    => 'DE',
        'vat_identifier'                  => '123456',
        'english_correspondence_language' => true,
        'website'                         => 'test.com',
        'fax'                             => '+49123456789',
        'phone'                           => '+49123456789',
        'email'                           => 'test@test.com',
        'billing_email_cc'                => 'test@test.com',
        'address'                         => 'test',
        'info'                            => 'info',
        'customer_properties'             => ['test' => 'test'],
        'tags'                            => ['test'],
        'user_id'                         => 933736920,
        'footer'                          => '<div>test</div>',
        'currency'                        => 'EUR',
        'identifier'                      => 'C-1234',
        'default_invoice_due_days'        => 20,
        'debit_number'                    => 10000,
        'iban'                            => 'CH3908704016075473007',
        'credit_number'                   => 70000,
    ];

    public function testCreate(): void
    {
        $this->mockResponse(200, json_encode($this->createParams));
        $company = $this->mocoClient->companies->create($this->createParams);
        $this->assertEquals('Company A', $company->name);
    }

    public function testCreateInvalidRequestException(): void
    {
        $params = $this->createParams;
        unset($params['name']);
        $this->expectException(InvalidRequestException::class);
        $this->mocoClient->companies->create($params);
    }

    public function testCreateInvalidResponse(): void
    {
        $this->mockResponse(500);
        $this->expectException(InvalidResponseException::class);
        $this->mocoClient->companies->create($this->createParams);
    }

    public function testGet(): void
    {
        $this->mockResponse(200, json_encode($this->createParams));
        $company = $this->mocoClient->companies->get(123);
        $this->assertEquals('Company A', $company->name);

        $this->mockResponse(200, json_encode([$this->createParams]));
        $users = $this->mocoClient->companies->get();
        $this->assertEquals('Company A', $users[0]->name);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->companies->get(1234);
    }

    public function testUpdate()
    {
        $params = $this->createParams;
        $params['name'] = 'changed';
        $this->mockResponse(200, json_encode($params));
        $company = $this->mocoClient->companies->update(123, $params);
        $this->assertEquals('changed', $company->name);

        $this->mockResponse(404);
        $this->expectException(NotFoundException::class);
        $this->mocoClient->companies->update(1234, $params);
    }

    public function testDelete(): void
    {
        $this->mockResponse(204);
        $this->assertNull($this->mocoClient->companies->delete(123));
    }
}
