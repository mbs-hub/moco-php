<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $date
 * @property float $hours
 * @property int $seconds
 * @property string $description
 * @property bool $billed
 * @property bool $billable
 * @property string $tag
 * @property string $remote_service
 * @property string $remote_id
 * @property string $remote_url
 * @property array $project
 * @property array $task
 * @property array $customer
 * @property array $user
 * @property float $hourly_rate
 * @property string $timer_started_at
 * @property string $created_at
 * @property string $updated_at
 */
class Activity extends AbstractMocoEntity
{
    public function getMandatoryFields(): array
    {
        return ['date', 'project_id', 'task_id', 'hours'];
    }
}
