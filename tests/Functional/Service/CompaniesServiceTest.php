<?php

namespace Tests\Functional\Service;

class CompaniesServiceTest extends AbstractServiceTest
{
    private array $createParams = [
        'name'                            => 'Company A',
        'type'                            => 'customer',
        'country_code'                    => 'DE',
        'vat_identifier'                  => 'DE123456789',
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

    public function testCreate(): int
    {
        /**
* if it was successful then the user is object is returned so
*/
        $this->createParams['identifier'] = 'C-1234' . time();
        $company = $this->mocoClient->companies->create($this->createParams);
        $this->assertEquals($company->name, 'Company A');
        return $company->id;
    }

    /**
     * @depends testCreate
     */
    public function testGet(int $id): int
    {
        $company = $this->mocoClient->companies->get($id);
        $this->assertEquals('Company A', $company->name);

        $companies = $this->mocoClient->companies->get();
        $this->assertIsArray($companies);
        return $id;
    }

    /**
     * @depends testGet
     */
    public function testUpdate(int $id): int
    {
        $updateParams = ['name' => 'new company name'];
        $company = $this->mocoClient->companies->update($id, $updateParams);
        $this->assertEquals('new company name', $company->name);
        return $id;
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(int $id): void
    {
        $this->assertNull($this->mocoClient->companies->delete($id));
    }
}
