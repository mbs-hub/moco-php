<?php

namespace Moco;

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;
use Moco\Service\AbstractService;
use Moco\Service\AccountServiceFactory;
use Moco\Service\ActivitiesService;
use Moco\Service\CommentsService;
use Moco\Service\CompaniesService;
use Moco\Service\ContactsService;
use Moco\Service\Deal\DealCategoryService;
use Moco\Service\Deal\DealService;
use Moco\Service\Invoice\InvoiceBookkeepingExportsService;
use Moco\Service\Invoice\InvoicePaymentsService;
use Moco\Service\Invoice\InvoiceRemindersService;
use Moco\Service\Invoice\InvoicesService;
use Moco\Service\Offer\OfferCustomerApprovalService;
use Moco\Service\Offer\OffersService;
use Moco\Service\PlanningEntriesService;
use Moco\Service\ProfileService;
use Moco\Service\Projects\ProjectContractsService;
use Moco\Service\Projects\ProjectExpensesService;
use Moco\Service\Projects\ProjectGroupsService;
use Moco\Service\Projects\ProjectPaymentScheduleService;
use Moco\Service\Projects\ProjectRecurringExpenseService;
use Moco\Service\Projects\ProjectsService;
use Moco\Service\Projects\ProjectTasksService;
use Moco\Service\Purchases\PurchaseBookkeepingExportsService;
use Moco\Service\Purchases\PurchaseBudgetsService;
use Moco\Service\Purchases\PurchaseCategoriesService;
use Moco\Service\Purchases\PurchaseDraftsService;
use Moco\Service\Purchases\PurchasePaymentsService;
use Moco\Service\Purchases\PurchasesService;
use Moco\Service\ReceiptsService;
use Moco\Service\ReportsService;
use Moco\Service\SchedulesService;
use Moco\Service\ServiceFactory;
use Moco\Service\TagsService;
use Moco\Service\UnitsService;
use Moco\Service\User\UserEmploymentsService;
use Moco\Service\User\UserHolidaysService;
use Moco\Service\User\UsersService;
use Moco\Service\User\UserWorkTimeAdjustmentsService;
use Moco\Service\VatCodeService;
use Moco\Service\WebHooksService;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

//TODO
//Purchases
//Units / Teams
//User Presences
//User Work Time Adjustments
//Vat Codes
//WebHooks

/**
 * @property UnitsService $units
 * @property UserEmploymentsService $userEmployments
 * @property UserHolidaysService $userHolidays
 * @property UserWorkTimeAdjustmentsService $userWorkTimeAdjustments
 * @property UsersService $users
 * @property CompaniesService $companies
 * @property AccountServiceFactory $account
 * @property ProjectsService $projects
 * @property ProjectTasksService $projectTasks
 * @property ActivitiesService $activities
 * @property CommentsService $comments
 * @property ContactsService $contacts
 * @property DealCategoryService $dealCategory
 * @property DealService $deal
 * @property InvoicesService $invoice
 * @property InvoiceBookkeepingExportsService $invoiceBookkeepingExport
 * @property InvoicePaymentsService $invoicePayments
 * @property InvoiceRemindersService $invoiceReminders
 * @property OffersService $offers
 * @property OfferCustomerApprovalService $offerCustomerApproval
 * @property PlanningEntriesService $planningEntries
 * @property ProfileService $profile
 * @property ReceiptsService $receipts
 * @property ReportsService $reports
 * @property SchedulesService $schedules
 * @property TagsService $tags
 * @property ProjectContractsService $projectContracts
 * @property ProjectExpensesService $projectExpenses
 * @property ProjectGroupsService $projectGroups
 * @property ProjectPaymentScheduleService $projectPaymentSchedule
 * @property ProjectRecurringExpenseService $projectRecurringExpense
 * @property PurchasesService $purchases
 * @property PurchaseBookkeepingExportsService $purchaseBookkeepingExports
 * @property PurchaseBudgetsService $purchaseBudgets
 * @property PurchaseCategoriesService $purchaseCategories
 * @property PurchaseDraftsService $purchaseDrafts
 * @property PurchasePaymentsService $purchasePayments
 * @property VatCodeService $vatCodes
 * @property WebHooksService $webHooks
 */
class MocoClient
{
    protected string $token;
    protected string $endpoint;
    protected ServiceFactory $serviceFactory;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $messageStream;
    protected ClientInterface $client;

    public function __construct(array $params)
    {
        if (isset($params['endpoint']) && isset($params['token'])) {
            $this->setEndpoint($params['endpoint']);
            $this->token = $params['token'];
        } else {
            throw new \Exception('Please provide endpoint and token');
        }

        $this->client = HttpClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->messageStream = Psr17FactoryDiscovery::findStreamFactory();
        $this->serviceFactory = new ServiceFactory($this);
    }

    public function __get(string $name): AbstractService|AccountServiceFactory|null
    {
        return $this->serviceFactory->__get($name);
    }

    public function request(string $method, string $endpoint, array $params = []): string
    {
        $request = $this->requestFactory->createRequest($method, $endpoint)
                                        ->withHeader('Authorization', $this->getAuthHeader())
                                        ->withHeader('Accept', 'application/json')
                                        ->withHeader('Content-Type', 'application/json')
                                        ->withBody($this->messageStream->createStream(json_encode($params)));
        $response = $this->client->sendRequest($request);

        return $this->createResponse($response);
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    protected function setEndpoint(string $endpoint): void
    {
        $lastChar = substr($endpoint, -1);
        if ($lastChar != '/') {
            $endpoint .= '/';
        }
        $this->endpoint = $endpoint;
    }

    protected function createResponse(ResponseInterface $response): string
    {
        if (in_array($response->getStatusCode(), range(200, 299))) {
            return $response->getBody()->getContents();
        } else {
            if (in_array($response->getStatusCode(), range(400, 499))) {
                if ($response->getStatusCode() === 404) {
                    throw new NotFoundException('Requested entity not found.');
                } else {
                    throw new InvalidRequestException(
                        $response->getBody()->getContents(),
                        $response->getStatusCode()
                    );
                }
            } else {
                throw new InvalidResponseException(
                    $response->getBody()->getContents(),
                    $response->getStatusCode()
                );
            }
        }
    }

    protected function getAuthHeader(): string
    {
        return 'Token token=' . $this->token;
    }
}
