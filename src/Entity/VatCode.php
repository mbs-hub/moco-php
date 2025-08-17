<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property float $tax
 * @property bool $reverse_charge
 * @property bool $intra_eu
 * @property bool $active
 * @property string $code
 * @property bool|null $print_gross_total
 * @property string|null $credit_account
 * @property string|null $debit_account
 */
class VatCode extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return [];
    }
}
