<?php

declare(strict_types=1);

namespace Moco\Service\Deal;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\DealCategory;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method DealCategory create(array $params)
 * @method DealCategory|DealCategory[]|null get(int|array|null $params = null)
 * @method DealCategory update(int $id, array $params)
 * @method void delete(int $id)
 */
class DealCategoryService extends AbstractService
{
    use Create;
    use Get;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'deal_categories';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new DealCategory();
    }
}
