<?php

declare(strict_types=1);

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\Profile;
use Moco\Service\Tarit\Get;

/**
 * @method Profile get(int|array|null $params = null)
 */
class ProfileService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'profile';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new Profile();
    }
}
