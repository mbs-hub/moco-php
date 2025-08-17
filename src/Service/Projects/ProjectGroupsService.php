<?php

declare(strict_types=1);

namespace Moco\Service\Projects;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\ProjectGroup;
use Moco\Service\AbstractService;
use Moco\Service\Tarit\Get;

/**
 * @method ProjectGroup|ProjectGroup[] get(int|array|null $params = null)
 */
class ProjectGroupsService extends AbstractService
{
    use Get;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'projects/groups';
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new ProjectGroup();
    }
}
