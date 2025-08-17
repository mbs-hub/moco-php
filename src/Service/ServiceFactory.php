<?php

namespace Moco\Service;

use Moco\MocoClient;
use Moco\Service\Deal\DealCategoryService;
use Moco\Service\Deal\DealService;
use Moco\Service\Invoice\InvoiceBookkeepingExportsService;
use Moco\Service\Invoice\InvoicePaymentsService;
use Moco\Service\Invoice\InvoiceRemindersService;
use Moco\Service\Invoice\InvoicesService;
use Moco\Service\Offer\OfferCustomerApprovalService;
use Moco\Service\Offer\OffersService;
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
use Moco\Service\User\UserEmploymentsService;
use Moco\Service\User\UserHolidaysService;
use Moco\Service\User\UserPresencesService;
use Moco\Service\User\UserWorkTimeAdjustmentsService;
use Moco\Service\User\UsersService;
use Moco\Service\VatCodeService;
use Moco\Service\WebHooksService;

use function array_key_exists;

class ServiceFactory
{
    private array $classMap = [
        'units' => UnitsService::class,
        'users' => UsersService::class,
        'companies' => CompaniesService::class,
        'account' => AccountServiceFactory::class,
        'projects' => ProjectsService::class,
        'projectTasks' => ProjectTasksService::class,
        'projectContracts' => ProjectContractsService::class,
        'projectExpenses' => ProjectExpensesService::class,
        'projectGroups' => ProjectGroupsService::class,
        'projectPaymentSchedule' => ProjectPaymentScheduleService::class,
        'projectRecurringExpense' => ProjectRecurringExpenseService::class,
        'activities' => ActivitiesService::class,
        'comments' => CommentsService::class,
        'contacts' => ContactsService::class,
        'dealCategory' => DealCategoryService::class,
        'deal' => DealService::class,
        'invoice' => InvoicesService::class,
        'invoiceBookkeepingExports' => InvoiceBookkeepingExportsService::class,
        'invoicePayments' => InvoicePaymentsService::class,
        'invoiceReminders' => InvoiceRemindersService::class,
        'offers' => OffersService::class,
        'offerCustomerApproval' => OfferCustomerApprovalService::class,
        'planningEntries' => PlanningEntriesService::class,
        'profile' => ProfileService::class,
        'receipts' => ReceiptsService::class,
        'reports' => ReportsService::class,
        'schedules' => SchedulesService::class,
        'tags' => TagsService::class,
        'purchases' => PurchasesService::class,
        'purchaseBookkeepingExports' => PurchaseBookkeepingExportsService::class,
        'purchaseBudgets' => PurchaseBudgetsService::class,
        'purchaseCategories' => PurchaseCategoriesService::class,
        'purchaseDrafts' => PurchaseDraftsService::class,
        'purchasePayments' => PurchasePaymentsService::class,
        'userEmployments' => UserEmploymentsService::class,
        'userHolidays' => UserHolidaysService::class,
        'userPresences' => UserPresencesService::class,
        'userWorkTimeAdjustments' => UserWorkTimeAdjustmentsService::class,
        'vatCodes' => VatCodeService::class,
        'webHooks' => WebHooksService::class
    ];

    protected array $services = [];
    private MocoClient $client;

    public function __construct(MocoClient $client)
    {
        $this->client = $client;
    }

    protected function getServiceClass(string $name): ?string
    {
        return array_key_exists($name, $this->classMap) ? $this->classMap[$name] : null;
    }

    public function __get(string $name): AbstractService|AccountServiceFactory|null
    {
        $serviceClass = $this->getServiceClass($name);
        if (!is_null($serviceClass)) {
            if (!array_key_exists($name, $this->services)) {
                $this->services[$name] = new $serviceClass($this->client);
            }

            return $this->services[$name];
        }

        return null;
    }
}
