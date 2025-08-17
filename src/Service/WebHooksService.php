<?php

namespace Moco\Service;

use Moco\Entity\AbstractMocoEntity;
use Moco\Entity\WebHook;
use Moco\Service\Tarit\Create;
use Moco\Service\Tarit\Delete;
use Moco\Service\Tarit\Get;
use Moco\Service\Tarit\Update;

/**
 * @method WebHook create(array $params)
 * @method WebHook|WebHook[]|null get(int|array|null $params = null)
 * @method WebHook update(int $id, array $params)
 * @method void delete(int $id)
 */
class WebHooksService extends AbstractService
{
    use Get;
    use Create;
    use Update;
    use Delete;

    private const ENDPOINT_PATH = 'account/web_hooks';

    protected function getEndpoint(): string
    {
        return $this->endpoint . self::ENDPOINT_PATH;
    }

    protected function getMocoObject(): AbstractMocoEntity
    {
        return new WebHook();
    }

    /**
     * Enable a webhook
     *
     * @param int $id The webhook ID
     * @return WebHook
     */
    public function enable(int $id): WebHook
    {
        $endpoint = $this->getEndpoint() . '/' . $id . '/enable';
        $result = $this->client->request('PUT', $endpoint);
        $data = json_decode($result);

        return $this->createMocoEntity($data, $this->getMocoObject());
    }

    /**
     * Disable a webhook
     *
     * @param int $id The webhook ID
     * @return WebHook
     */
    public function disable(int $id): WebHook
    {
        $endpoint = $this->getEndpoint() . '/' . $id . '/disable';
        $result = $this->client->request('PUT', $endpoint);
        $data = json_decode($result);

        return $this->createMocoEntity($data, $this->getMocoObject());
    }
}
