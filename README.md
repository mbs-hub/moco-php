# MOCO PHP SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mbs-hub/moco-php.svg?style=flat-square)](https://packagist.org/packages/mbs-hub/moco-php)
[![Build Status](https://github.com/mbs-hub/moco-php/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/mbs-hub/moco-php/actions?query=branch%3Amain)


The **MOCO PHP SDK** provides convenient access to the [MOCO API](https://everii-group.github.io/mocoapp-api-docs/) from applications written in PHP. It includes a comprehensive set of pre-built services and models to simplify integration with MOCO's time tracking, project management, and invoicing platform.

## ✨ Features

- **🏗️ Service-Oriented Architecture**: Clean, organized service classes for each API endpoint
- **📋 Comprehensive Entity System**: Strongly typed models for all MOCO resources
- **🔧 PSR Standards Compliant**: Built on PSR-7 (HTTP Message), PSR-17 (HTTP Factories), and PSR-18 (HTTP Client)
- **⚡ Automatic Parameter Validation**: Built-in validation for required fields
- **🔌 Flexible HTTP Client**: Works with any PSR-18 compatible HTTP client
- **🎯 Full MOCO API Coverage**: Support for all major MOCO features including users, projects, activities, invoices, and more

## 📑 Table of Contents

- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Authentication](#-authentication)
- [Usage Examples](#-usage-examples)
  - [Users Management](#users-management)
  - [Companies & Contacts](#companies--contacts)
  - [Projects & Tasks](#projects--tasks)
  - [Activities & Time Tracking](#activities--time-tracking)
  - [Invoices & Payments](#invoices--payments)
  - [Offers & Deals](#offers--deals)
- [API Reference](#-api-reference)
- [Advanced Configuration](#-advanced-configuration)
- [Error Handling](#-error-handling)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [Versioning](#-versioning)
- [License](#-license)

## 🔧 Requirements

- **PHP 7.4** or higher
- **Required Extensions**: `curl`, `json`, `mbstring`
- **Composer** for dependency management
- A valid [MOCO account](https://www.mocoapp.com/) and API token

### Dependencies

This library uses the following PSR-compatible packages:

- `psr/http-message` - HTTP message interfaces (PSR-7)
- `psr/http-client-implementation` - HTTP client implementation (PSR-18)
- `php-http/httplug` - HTTP client abstraction
- `php-http/discovery` - Automatic HTTP client discovery
- `symfony/http-client` - Default HTTP client implementation

## 📦 Installation

### Via Composer (Recommended)

Install the latest version with [Composer](https://getcomposer.org/):

```bash
composer require mehdibagheri/moco-php
```

## 🚀 Quick Start

Get up and running with the MOCO API in minutes:

```php
<?php

require_once 'vendor/autoload.php';

use Moco\MocoClient;

// Initialize the client
$moco = new MocoClient([
    'endpoint' => 'https://your-company.mocoapp.com/api/v1/',
    'token' => 'your-api-token-here'
]);

// Create a new user
$user = $moco->users->create([
    'firstname' => 'John',
    'lastname' => 'Doe',
    'email' => 'john.doe@company.com',
    'password' => 'secure-password',
    'unit_id' => 123456
]);

echo "User created with ID: " . $user->id;

// Get all projects
$projects = $moco->projects->get();
foreach ($projects as $project) {
    echo "Project: " . $project->name . " (ID: " . $project->id . ")\n";
}

// Log time activity
$activity = $moco->activities->create([
    'date' => '2024-01-15',
    'hours' => 8.5,
    'description' => 'Working on API integration',
    'project_id' => 789,
    'task_id' => 456,
    'user_id' => $user->id
]);

echo "Activity logged: " . $activity->id;
```

## 🔐 Authentication

The MOCO PHP SDK uses token-based authentication. You'll need:

1. **API Token**: Generate one in your MOCO account settings
2. **Endpoint URL**: Your company-specific MOCO API endpoint

### Getting Your Credentials

1. Log into your MOCO account
2. Go to **Settings** → **API**
3. Generate a new **API Token**
4. Note your **API Endpoint** (usually `https://your-company.mocoapp.com/api/v1/`)

### Configuration Options

```php
// Basic configuration
$moco = new MocoClient([
    'endpoint' => 'https://your-company.mocoapp.com/api/v1/',
    'token' => 'your-api-token'
]);

// The endpoint URL will automatically have a trailing slash added if missing
```

### Security Best Practices

- **Never commit API tokens** to version control
- Use **environment variables** for sensitive credentials:

```php
// Using environment variables
$moco = new MocoClient([
    'endpoint' => $_ENV['MOCO_ENDPOINT'],
    'token' => $_ENV['MOCO_TOKEN']
]);
```

- **Regenerate tokens** regularly
- **Limit token permissions** to only what your application needs

## 📘 Usage Examples

### Users Management

```php
// Create a new user
$user = $moco->users->create([
    'firstname' => 'Alice',
    'lastname' => 'Smith',
    'email' => 'alice.smith@company.com',
    'password' => 'secure-password-123',
    'unit_id' => 123456,
    'active' => true,
    'language' => 'en',
    'mobile_phone' => '+1-555-0123',
    'tags' => ['developer', 'frontend']
]);

// Get all users
$users = $moco->users->get();

// Get a specific user
$user = $moco->users->get(12345);

// Update user
$updatedUser = $moco->users->update(12345, [
    'firstname' => 'Alicia',
    'mobile_phone' => '+1-555-0124'
]);

// Get user performance report
$performance = $moco->users->getPerformanceReport(12345, [
    'from' => '2024-01-01',
    'to' => '2024-12-31'
]);

// Delete user
$moco->users->delete(12345);
```

### Companies & Contacts

```php
// Create a company
$company = $moco->companies->create([
    'name' => 'Acme Corporation',
    'website' => 'https://acme.com',
    'currency' => 'USD',
    'country_code' => 'US',
    'address' => "123 Main St\nAnytown, ST 12345",
    'phone' => '+1-555-0199',
    'tags' => ['client', 'enterprise']
]);

// Create a contact for the company
$contact = $moco->contacts->create([
    'firstname' => 'Jane',
    'lastname' => 'Johnson',
    'email' => 'jane@acme.com',
    'phone' => '+1-555-0198',
    'company_id' => $company->id,
    'tags' => ['primary-contact']
]);

// Get all companies with pagination
$companies = $moco->companies->get([
    'page' => 1,
    'tags' => 'client'
]);
```

### Projects & Tasks

```php
// Create a project
$project = $moco->projects->create([
    'name' => 'Website Redesign',
    'company_id' => 12345,
    'user_id' => 67890,  // Project leader
    'budget' => 50000.00,
    'currency' => 'USD',
    'start_date' => '2024-01-15',
    'finish_date' => '2024-06-30',
    'tags' => ['web-development', 'design']
]);

// Create project tasks
$task = $moco->projectTasks->create([
    'name' => 'Frontend Development',
    'project_id' => $project->id,
    'billable' => true,
    'active' => true
]);

// Get project details
$projectDetails = $moco->projects->get($project->id);

// Get all tasks for a project
$tasks = $moco->projectTasks->get(['project_id' => $project->id]);

// Update project
$updatedProject = $moco->projects->update($project->id, [
    'budget' => 55000.00,
    'tags' => ['web-development', 'design', 'responsive']
]);
```

### Activities & Time Tracking

```php
// Log a time entry
$activity = $moco->activities->create([
    'date' => '2024-01-16',
    'hours' => 6.5,
    'description' => 'Implemented user authentication system',
    'project_id' => 12345,
    'task_id' => 67890,
    'user_id' => 11111,
    'billable' => true,
    'tag' => 'development'
]);

// Get activities for a date range
$activities = $moco->activities->get([
    'from' => '2024-01-01',
    'to' => '2024-01-31',
    'user_id' => 11111
]);

// Update time entry
$updatedActivity = $moco->activities->update($activity->id, [
    'hours' => 7.0,
    'description' => 'Implemented user authentication system with 2FA'
]);

// Get activities by project
$projectActivities = $moco->activities->get([
    'project_id' => 12345
]);
```

### Invoices & Payments

```php
// Create an invoice
$invoice = $moco->invoice->create([
    'company_id' => 12345,
    'project_id' => 67890,
    'recipient_address' => "Acme Corporation\n123 Main St\nAnytown, ST 12345",
    'date' => '2024-01-30',
    'due_date' => '2024-02-29',
    'currency' => 'USD',
    'tax' => 8.5,
    'discount' => 5.0,
    'items' => [
        [
            'type' => 'item',
            'title' => 'Website Development',
            'quantity' => 1,
            'unit' => 'month',
            'unit_price' => 5000.00
        ]
    ]
]);

// Get invoice payments
$payments = $moco->invoicePayments->get(['invoice_id' => $invoice->id]);

// Add payment to invoice
$payment = $moco->invoicePayments->create([
    'invoice_id' => $invoice->id,
    'date' => '2024-02-15',
    'amount' => 5000.00,
    'currency' => 'USD'
]);

// Get all invoices
$invoices = $moco->invoice->get([
    'status' => 'paid',
    'from' => '2024-01-01',
    'to' => '2024-12-31'
]);
```

### Offers & Deals

```php
// Create a deal
$deal = $moco->deal->create([
    'name' => 'Enterprise Website Project',
    'money' => 75000.00,
    'currency' => 'USD',
    'reminder_date' => '2024-02-01',
    'user_id' => 11111,
    'company_id' => 12345,
    'status' => 'potential',
    'tags' => ['enterprise', 'web-development']
]);

// Create an offer
$offer = $moco->offers->create([
    'company_id' => 12345,
    'project_id' => 67890,
    'recipient_address' => "Acme Corporation\n123 Main St\nAnytown, ST 12345",
    'date' => '2024-01-15',
    'currency' => 'USD',
    'tax' => 8.5,
    'discount' => 10.0,
    'items' => [
        [
            'type' => 'title',
            'title' => 'Website Development Package'
        ],
        [
            'type' => 'description',
            'description' => 'Complete website redesign and development'
        ],
        [
            'type' => 'item',
            'title' => 'Frontend Development',
            'quantity' => 120,
            'unit' => 'hours',
            'unit_price' => 125.00
        ]
    ]
]);

// Get deal categories
$dealCategories = $moco->dealCategory->get();
```

## 📚 API Reference

### Available Services

The MOCO PHP SDK provides the following services through the main client:

#### Core Services
- `$moco->users` - User management and operations
- `$moco->companies` - Company management
- `$moco->contacts` - Contact management
- `$moco->projects` - Project management
- `$moco->projectTasks` - Project task management
- `$moco->activities` - Time tracking and activities

#### Account Services
Access account-related services via `$moco->account`:
- `$moco->account->customProperties` - Custom field management
- `$moco->account->catalogs` - Service catalog management
- `$moco->account->fixedCosts` - Fixed cost management
- `$moco->account->hourlyRates` - Hourly rate management
- `$moco->account->internalHourlyRates` - Internal rate management

#### Business Services
- `$moco->invoice` - Invoice management
- `$moco->invoicePayments` - Invoice payment tracking
- `$moco->invoiceReminders` - Invoice reminder management
- `$moco->offers` - Offer/quote management
- `$moco->deal` - Deal pipeline management
- `$moco->purchases` - Purchase management

#### Planning & Reporting
- `$moco->planningEntries` - Resource planning
- `$moco->schedules` - Schedule management
- `$moco->reports` - Report generation

#### System Services
- `$moco->tags` - Tag management
- `$moco->units` - Team/unit management
- `$moco->vatCodes` - VAT code management
- `$moco->webHooks` - Webhook management

### Common Methods

All services provide standard CRUD operations where applicable:

```php
// Create a resource
$resource = $service->create(array $data);

// Get all resources (with optional filters)
$resources = $service->get(array $filters = []);

// Get a specific resource by ID
$resource = $service->get(int $id);

// Update a resource
$updated = $service->update(int $id, array $data);

// Delete a resource
$service->delete(int $id);
```

Each entity provides property access and automatic type conversion:

```php
$user = $moco->users->get(12345);
echo $user->firstname; // string
echo $user->active;    // boolean
echo $user->created_at; // string (ISO 8601)
```

### Filtering and Pagination

Most services support filtering and pagination:

```php
// Date range filtering
$activities = $moco->activities->get([
    'from' => '2024-01-01',
    'to' => '2024-01-31'
]);

// Pagination
$users = $moco->users->get([
    'page' => 2
]);

// Tag filtering
$projects = $moco->projects->get([
    'tags' => 'active,web-development'
]);

// Status filtering
$invoices = $moco->invoice->get([
    'status' => 'open'
]);
```

## ⚙️ Advanced Configuration

### Custom HTTP Client

The SDK uses HTTP client discovery by default, but you can provide your own PSR-18 compatible client:

```php
use Symfony\Component\HttpClient\Psr18Client;
use Moco\MocoClient;

$httpClient = new Psr18Client();
$moco = new MocoClient([
    'endpoint' => 'https://your-company.mocoapp.com/api/v1/',
    'token' => 'your-token'
]);

// The client will be discovered automatically, but you can also 
// configure it through dependency injection if needed
```

### Request Timeouts

Configure request timeouts through your HTTP client:

```php
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

$httpClient = new Psr18Client(
    HttpClient::create([
        'timeout' => 30,      // 30 seconds timeout
        'max_duration' => 60  // Maximum request duration
    ])
);
```

### Boolean Parameter Handling

The SDK automatically converts boolean values to strings for API compatibility:

```php
$user = $moco->users->create([
    'firstname' => 'John',
    'lastname' => 'Doe',
    'active' => true,  // Automatically converted to 'true' string
    'extern' => false  // Automatically converted to 'false' string
]);
```

### Parameter Validation

The SDK validates required parameters automatically:

```php
try {
    $user = $moco->users->create([
        'firstname' => 'John'
        // Missing required fields: lastname, email, password, unit_id
    ]);
} catch (Moco\Exception\InvalidRequestException $e) {
    echo "Missing required parameters: " . $e->getMessage();
}
```

## 🚨 Error Handling

The SDK provides specific exception types for different error scenarios:

### Exception Types

- `Moco\Exception\InvalidRequestException` - Malformed requests (4xx status codes)
- `Moco\Exception\InvalidResponseException` - Server errors (5xx status codes)
- `Moco\Exception\NotFoundException` - Resource not found (404 status code)

### Handling Errors

```php
use Moco\Exception\InvalidRequestException;
use Moco\Exception\InvalidResponseException;
use Moco\Exception\NotFoundException;

try {
    $user = $moco->users->get(999999);
} catch (NotFoundException $e) {
    echo "User not found: " . $e->getMessage();
} catch (InvalidRequestException $e) {
    echo "Invalid request: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
} catch (InvalidResponseException $e) {
    echo "Server error: " . $e->getMessage();
    echo "Status code: " . $e->getCode();
}
```

### Common Error Scenarios

1. **Authentication Issues**: Invalid token or endpoint
2. **Validation Errors**: Missing required parameters
3. **Rate Limiting**: Too many requests (implement backoff strategy)
4. **Resource Not Found**: Accessing non-existent resources
5. **Permission Errors**: Insufficient permissions for the operation

### Best Practices

```php
// Implement retry logic for rate limiting
$maxRetries = 3;
$retry = 0;

while ($retry < $maxRetries) {
    try {
        $result = $moco->users->get();
        break;
    } catch (InvalidResponseException $e) {
        if ($e->getCode() === 429) { // Rate limited
            $retry++;
            sleep(pow(2, $retry)); // Exponential backoff
        } else {
            throw $e;
        }
    }
}
```

### Test Configuration

Functional tests require valid MOCO API credentials. Create a `.env` file:

```env
MOCO_ENDPOINT=https://your-test-company.mocoapp.com/api/v1/
MOCO_TOKEN=your-test-api-token
```

### Code Quality

Run code quality tools:

```bash
# Static analysis with Psalm
./vendor/bin/psalm

# Code style checking (PSR-12)
./vendor/bin/phpcs src/ tests/ --standard=PSR12

# Static analysis with PHPStan
./vendor/bin/phpstan analyse src/
```

### Coverage Configuration

The project is already configured with PHPUnit coverage via `phpunit.xml`:

```xml
<coverage cacheDirectory=".phpunit.cache/code-coverage"
          processUncoveredFiles="true">
    <include>
        <directory suffix=".php">src</directory>
    </include>
</coverage>
```

### Coverage Targets

We aim for high code coverage standards:
- **Unit Tests**: >90% line coverage
- **Functional Tests**: >90% integration coverage  
- **Overall**: >90% combined coverage
- **Critical Paths**: 100% coverage for authentication, validation, and error handling

## 🤝 Contributing

We welcome contributions to the MOCO PHP SDK! Here's how you can help:

### Development Workflow

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Make** your changes
4. **Add** tests for new functionality
5. **Ensure** all tests pass (`./vendor/bin/phpunit`)
6. **Run** code quality tools (`./vendor/bin/psalm`, `./vendor/bin/phpcs`)
7. **Commit** your changes (`git commit -m 'Add amazing feature'`)
8. **Push** to your branch (`git push origin feature/amazing-feature`)
9. **Open** a Pull Request

### Code Standards

- Follow **PSR-12** coding standards
- Include **comprehensive tests** for new features
- Maintain **backward compatibility** when possible
- Update **documentation** for API changes
- Use **meaningful commit messages**

### Adding New Services

When adding support for new MOCO API endpoints:

1. Create the service class extending `AbstractService`
2. Create the corresponding entity class
3. Add the service to `ServiceFactory`
4. Include comprehensive tests
5. Update documentation

### Reporting Issues

Found a bug or have a feature request?

1. **Check** existing issues first
2. **Create** a detailed bug report with:
   - PHP version
   - SDK version
   - Code example that reproduces the issue
   - Expected vs actual behavior

## 🏷️ Versioning

The MOCO PHP SDK follows [Semantic Versioning (SemVer)](https://semver.org/) for predictable and reliable releases:

### Version Format: `MAJOR.MINOR.PATCH`

- **MAJOR**: Breaking changes that require code updates
- **MINOR**: New features that are backwards compatible
- **PATCH**: Bug fixes and improvements

### Current Version: `1.0.0`

### Supported PHP Versions

We officially support and test against:
- **PHP 7.4** - Minimum supported version
- **PHP 8.0** - Fully supported
- **PHP 8.1** - Fully supported  
- **PHP 8.2** - Fully supported
- **PHP 8.3** - Fully supported

## 📄 License

The MOCO PHP SDK is open-sourced software licensed under the [MIT license](LICENSE).

---

## Resources

- 📖 **[MOCO API Documentation](https://everii-group.github.io/mocoapp-api-docs/)**
- 🌐 **[MOCO Website](https://www.mocoapp.com/)**
