<?php

namespace Moco\Service\Projects;

use Moco\Exception\InvalidRequestException;

trait SetProjectId
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private int $projectId;
    private function setProjectId(array $params): void
    {
        if (empty($params['project_id'])) {
            throw new InvalidRequestException('please provide project_id!');
        }
        $this->projectId = $params['project_id'];
    }
}
