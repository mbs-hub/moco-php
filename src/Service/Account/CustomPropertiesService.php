<?php

namespace Moco\Service\Account;

use Moco\Entity\CustomProperty;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Get;

/**
 * @method CustomProperty|array|null get(int|array|null $params = null)
 */
class CustomPropertiesService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'account/custom_properties';
    }

    protected function getMocoObject(): CustomProperty
    {
        return new CustomProperty();
    }
}
