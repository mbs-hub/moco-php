<?php

namespace Moco\Service;

use Moco\Exception\InvalidRequestException;
use Moco\MocoClient;
//use Moco\Service\Account\CatalogServices;
use Moco\Service\Account\CatalogServices;
use Moco\Service\Account\CustomPropertiesService;
use Moco\Service\Account\FixedCostsService;
use Moco\Service\Account\HourlyRatesService;
use Moco\Service\Account\InternalHourlyRatesService;

/**
 * @property CatalogServices $catalogServices
 * @property CustomPropertiesService $customProperties
 * @property FixedCostsService $fixedCosts
 * @property HourlyRatesService $hourlyRates
 * @property InternalHourlyRatesService $internalHourlyRates
 */
class AccountServiceFactory
{
    private MocoClient $client;

    public function __construct(MocoClient $client)
    {
        $this->client = $client;
    }

    private array $services = [
      'catalogServices' => CatalogServices::class,
      'customProperties' => CustomPropertiesService::class,
      'fixedCosts' => FixedCostsService::class,
      'hourlyRates' => HourlyRatesService::class,
      'internalHourlyRates' => InternalHourlyRatesService::class
    ];

    public function __get(string $name): AbstractService
    {
        $class = \array_key_exists($name, $this->services) ? $this->services[$name] : null;
        if (!empty($class)) {
            return new $class($this->client);
        }
        throw new InvalidRequestException('no such a service ' . $name);
    }
}
