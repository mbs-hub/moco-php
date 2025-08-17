<?php

namespace Moco\Entity;

/**
 * @property int $id
 * @property string $target
 * @property string $event
 * @property string $hook
 * @property bool $disabled
 * @property string $created_at
 * @property string $updated_at
 */
class WebHook extends AbstractMocoEntity
{
    public const TARGET_ACTIVITY = 'Activity';
    public const TARGET_COMPANY = 'Company';
    public const TARGET_CONTACT = 'Contact';
    public const TARGET_PROJECT = 'Project';
    public const TARGET_INVOICE = 'Invoice';
    public const TARGET_OFFER = 'Offer';
    public const TARGET_DEAL = 'Deal';
    public const TARGET_EXPENSE = 'Expense';

    public const EVENT_CREATE = 'create';
    public const EVENT_UPDATE = 'update';
    public const EVENT_DELETE = 'delete';

    public function getMandatoryFields(): array
    {
        return ['target', 'event', 'hook'];
    }
}
