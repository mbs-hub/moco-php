<?php

namespace Moco\Service;

use Moco\Entity\Contact;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

class ContactsService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    protected function getEndpoint(): string
    {
        return $this->endpoint . 'contacts/people';
    }

    protected function getMocoObject(): Contact
    {
        return new Contact();
    }
}
