# Senior Developer - Practical Assessment Submission

This repository contains the solution for the practical assessment, implementing a production-ready RSS Feed Reader application using **Domain-Driven Design (DDD)** principles and clean architecture.

## Challenge Chosen

I have implemented **Option 2: RSS Feed Reader**.

This option was chosen because it provides an excellent opportunity to demonstrate a range of skills relevant to a senior developer role, including database design, interacting with external services, implementing background processing strategies, and building a reactive frontend in a multi-tenant context.


## Setup

### 1. Automated setup (recommended)

Run the helper script to install dependencies, create the `.env`, generate the key, provision the SQLite database, apply migrations, and launch the Vite dev server in the background:

```bash
./scripts/setup.sh
```

The script writes Vite output to `storage/logs/vite-dev.log` and leaves it running in the background. **Run `php artisan serve` in a separate terminal** after the script completes to bring up the Laravel server.

> Requirements: PHP 8.3+, Composer, Node.js `>=20.19` or `>=22.12` (see `.nvmrc`), npm, and SQLite.

### 2. Manual setup

1. **Clone the repository**
    ```bash
    git clone <repository-url>
    cd populytics-assessment
    ```
2. **Install dependencies**

    **Node.js requirement:** Vite 7 needs Node.js `>=20.19` (LTS) or `>=22.12`. The repository ships with an `.nvmrc` set to `22.12.0`; run `nvm use` (or install that version manually) before installing JS dependencies.
    ```bash
    composer install
    npm install
    ```
3. **Configure environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    ```
4. **Run database migrations**
    ```bash
    php artisan migrate
    ```
5. **Run the application**
    ```bash
    # Start the Vite development server
    npm run dev
    ```
    The application will be available at `http://127.0.0.1:8000` (or the address provided by `php artisan serve`).
6. **Start queue worker** (optional but recommended)
    ```bash
    php artisan queue:work
    ```

    **Note:** If you don't start a queue worker, you can still process feeds synchronously using the `--sync` flag (see Usage section below).
7. **Usage**
    * Navigate to the application and register a new user.
    * Go to the `/feeds` page.
    * Add a new RSS feed using the form (e.g., `https://www.php.net/releases/feed.php`).
        * When a feed is created, it automatically queues a job to process the feed (if queue worker is running)
    * To process feeds manually:
        ```bash
        # Process all feeds asynchronously (requires queue worker)
        php artisan feeds:process

        # Process all feeds synchronously (no queue worker needed)
        php artisan feeds:process --sync
        ```
    * Refresh the page to see the newly fetched feed items.

## Architecture Overview

This application follows **Domain-Driven Design (DDD)** principles with a **Clean Architecture** structure, demonstrating senior-level architectural patterns and best practices:

### Layered Architecture

The application is organized into four distinct layers, each with clear responsibilities:

#### 1. **Domain Layer** (`src/Domain/RssFeed/`)
- **Entities**: Core business objects (`Feed`, `FeedItem`) with rich domain logic
- **Value Objects**: Immutable objects representing domain concepts (`FeedUrl`, `FeedName`, `EntryId`) with built-in validation
- **Domain Services**: Interfaces for domain-specific operations (`RssFeedValidatorInterface`)
- **Domain Events**: Events that represent significant domain occurrences (`FeedCreated`, `FeedProcessed`)
- **Exceptions**: Domain-specific exceptions for better error handling

#### 2. **Application Layer** (`src/Application/RssFeed/`)
- **Use Cases**: Business logic orchestration (e.g., `CreateFeedUseCase`, `ProcessFeedUseCase`, `ListFeedItemsUseCase`)
- **DTOs**: Data Transfer Objects for clean data flow between layers
- **Interfaces**: Repository and service interfaces defining contracts (`FeedRepositoryInterface`, `RssFeedParserInterface`)

#### 3. **Infrastructure Layer** (`src/Infrastructure/RssFeed/`)
- **Repositories**: Concrete implementations using Eloquent ORM (`EloquentFeedRepository`, `EloquentFeedItemRepository`)
- **Services**: External service integrations (`SimpleXmlRssFeedParser`, `HttpRssFeedValidator`)
- Handles all technical concerns (HTTP, XML parsing, database access)

#### 4. **Presentation Layer** (`app/Http/`)
- **Controllers**: Thin controllers that delegate to use cases
- **Requests**: Form request validation (`CreateFeedRequest`)
- **Jobs**: Queued jobs for asynchronous processing (`ProcessFeedJob`)

### Key Design Patterns

1. **Repository Pattern**: Abstracts data access, making the domain layer persistence-agnostic
2. **Use Case Pattern**: Encapsulates business logic in single-purpose classes
3. **Value Objects**: Ensures data integrity and immutability at the domain level
4. **Event-Driven Architecture**: Decouples components using domain events
5. **Dependency Inversion**: Application layer depends on interfaces, not implementations
6. **Single Responsibility Principle**: Each class has one clear purpose

### Benefits of This Architecture

- **Testability**: Each layer can be tested independently with mocks/stubs
- **Maintainability**: Clear separation of concerns makes the codebase easier to understand and modify
- **Scalability**: Easy to swap implementations (e.g., replace Eloquent with another ORM)
- **Domain Focus**: Business logic is isolated from technical concerns
- **Type Safety**: Strong typing with value objects and strict types

## Design Choices

### Backend Architecture

*   **Domain-Driven Design (DDD)**: The application follows DDD principles with clear boundaries between domain, application, and infrastructure layers. This ensures business logic remains independent of technical concerns.

*   **Value Objects**: Core domain concepts like `FeedUrl`, `FeedName`, and `EntryId` are implemented as value objects with built-in validation, ensuring data integrity at the domain level.

*   **Repository Pattern**: Data access is abstracted through repository interfaces, making the domain layer persistence-agnostic and easily testable.

*   **Use Cases**: Business operations are encapsulated in dedicated use case classes, promoting single responsibility and reusability.

*   **Event-Driven Architecture**:
    *   When a feed is created, a `FeedCreated` event is dispatched
    *   An event listener automatically queues a job to process the feed asynchronously
    *   This decouples feed creation from feed processing, improving responsiveness

*   **Queued Jobs**: Feed processing is handled asynchronously through queued jobs (`ProcessFeedJob`), ensuring:
    *   Web requests remain responsive
    *   Failed jobs can be retried automatically
    *   Better scalability for processing multiple feeds

*   **Feed Processing Strategy**:
    *   The `feeds:process` command can process feeds synchronously (`--sync` flag) or asynchronously (default)
    *   Supports both one-time processing and automated scheduling via Laravel's task scheduler

*   **Error Handling**: Domain-specific exceptions provide clear error messages and proper HTTP responses.

*   **Data Model:**
    *   `Feeds` table stores user-subscribed RSS feeds with a `last_processed_at` timestamp
    *   `FeedItems` table stores parsed feed entries with unique constraints on `feed_id` and `entry_id`
    *   Each feed belongs to a user, ensuring proper multi-tenant data isolation

*   **Frontend Architecture:**
    *   The application uses the existing **Inertia.js with Vue 3** stack, providing a smooth, single-page application feel without the complexity of managing a separate API client.
    *   A single Vue component (`pages/Feeds/Index.vue`) manages both the form for adding new feeds and the display of aggregated feed items.
    *   Inertia's `useForm` helper is used for seamless form submission and validation error handling.
    *   **Tailwind CSS** is used for styling to maintain a clean and consistent UI.

## Technical Highlights

### What Makes This Implementation Senior-Level?

1. **Clean Architecture**: Strict separation of concerns with clear layer boundaries
2. **Domain-Driven Design**: Rich domain models with encapsulated business logic
3. **SOLID Principles**: Adherence to Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, and Dependency Inversion
4. **Type Safety**: Strict types throughout (`declare(strict_types=1)`) and strong typing with value objects
5. **Testability**: Architecture designed for easy unit and integration testing
6. **Scalability**: Asynchronous job processing, event-driven architecture
7. **Maintainability**: Clear code organization, dependency injection, interface-based design
8. **Error Handling**: Domain-specific exceptions with proper error propagation
9. **Validation**: Multi-layer validation (HTTP request, domain value objects, use cases)

### Architecture Decisions

- **Why DDD?**: RSS feed reader is a domain-rich application. DDD helps maintain business logic clarity as the application grows.
- **Why Value Objects?**: `FeedUrl`, `EntryId`, etc. have business rules (validation, immutability) that belong at the domain level.
- **Why Events?**: Feed creation and processing are separate concerns. Events allow decoupling and easy extension (e.g., sending notifications, analytics).
- **Why Queued Jobs?**: RSS feed parsing can be slow and unreliable. Jobs enable retry logic, better resource management, and improved user experience.

# Project Structure

This document explains the project structure and folder organization.

## Directory Layout

```
populytics-assessment/
├── app/                    # Laravel framework code (Presentation Layer)
│   ├── Console/           # Artisan commands
│   ├── Http/              # HTTP layer (Controllers, Requests, Middleware)
│   ├── Jobs/              # Queued jobs
│   ├── Listeners/         # Event listeners
│   ├── Models/            # Eloquent models (Infrastructure persistence)
│   └── Providers/         # Service providers
│
├── src/                    # Domain-driven design code (Framework-agnostic)
│   ├── Domain/            # Domain layer (Business logic)
│   │   └── RssFeed/
│   │       ├── Entities/           # Domain entities
│   │       ├── ValueObjects/       # Value objects
│   │       ├── Events/             # Domain events
│   │       ├── Exceptions/         # Domain exceptions
│   │       └── DomainServices/     # Domain service interfaces
│   │
│   ├── Application/       # Application layer (Use cases)
│   │   └── RssFeed/
│   │       ├── UseCases/           # Business use cases
│   │       ├── DTOs/               # Data Transfer Objects
│   │       ├── Repositories/       # Repository interfaces
│   │       └── Services/           # Application service interfaces
│   │
│   └── Infrastructure/    # Infrastructure layer (Technical implementation)
│       └── RssFeed/
│           ├── Repositories/       # Repository implementations
│           └── Services/           # Service implementations
│
├── database/              # Database migrations, seeders, factories
├── resources/             # Frontend assets (Vue, CSS, etc.)
├── routes/                # Route definitions
└── tests/                 # Test suites
```

## Namespace Organization

### `App\` Namespace
- **Location**: `app/` directory
- **Purpose**: Framework-specific code
- **Contains**: Controllers, Requests, Jobs, Listeners, Service Providers, Models

### `Populytics\` Namespace
- **Location**: `src/` directory
- **Purpose**: Framework-agnostic business logic
- **Contains**: Domain, Application, and Infrastructure layers

## Why `src/` Instead of `app/`?

1. **Separation of Concerns**: The `src/` directory clearly separates business logic from framework code
2. **Framework Independence**: Domain and Application layers are framework-agnostic
3. **Clear Boundaries**: Makes it obvious what is business logic vs. what is Laravel-specific
4. **Reusability**: Business logic in `src/` could theoretically be extracted to a library or reused

## Why `Domain` (Singular)?

- **Standard Convention**: DDD convention uses `Domain` (singular) to represent "the domain layer" as a concept
- **Consistency**: Matches established DDD patterns and Laravel DDD implementations
- **Clarity**: Indicates one domain layer, while multiple bounded contexts (like `RssFeed`) exist within it

## Namespace Structure

```
Populytics\
├── Domain\
│   └── RssFeed\
│       ├── Entities\
│       ├── ValueObjects\
│       ├── Events\
│       ├── Exceptions\
│       └── DomainServices\
│
├── Application\
│   └── RssFeed\
│       ├── UseCases\
│       ├── DTOs\
│       ├── Repositories\
│       └── Services\
│
└── Infrastructure\
    └── RssFeed\
        ├── Repositories\
        └── Services\
```

## Best Practices

1. **Domain Layer** (`src/Domain/`):
    - No dependencies on Laravel
    - Pure PHP with only standard library dependencies
    - Business logic only

2. **Application Layer** (`src/Application/`):
    - Depends only on Domain layer
    - Interfaces for infrastructure
    - Use cases orchestrate business logic

3. **Infrastructure Layer** (`src/Infrastructure/`):
    - Implements Application layer interfaces
    - Can use Laravel/Eloquent
    - Technical implementations

4. **Presentation Layer** (`app/Http/`):
    - Depends on Application layer
    - Framework-specific (Laravel Controllers, Requests)
    - Thin layer that delegates to use cases


# Architecture Documentation

This document provides a detailed overview of the Domain-Driven Design (DDD) architecture implemented in the RSS Feed Reader application.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Layer Structure](#layer-structure)
3. [Domain Layer](#domain-layer)
4. [Application Layer](#application-layer)
5. [Infrastructure Layer](#infrastructure-layer)
6. [Presentation Layer](#presentation-layer)
7. [Data Flow](#data-flow)
8. [Dependency Injection](#dependency-injection)

## Architecture Overview

The application follows **Clean Architecture** principles with **Domain-Driven Design** patterns. The architecture is organized into four distinct layers:

```
┌─────────────────────────────────────────┐
│      Presentation Layer (HTTP)          │
│   Controllers, Requests, Jobs           │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│      Application Layer                  │
│   Use Cases, DTOs, Interfaces           │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│      Domain Layer                       │
│   Entities, Value Objects, Events       │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│      Infrastructure Layer               │
│   Repositories, Services, ORM           │
└─────────────────────────────────────────┘
```

### Dependency Rule

Dependencies point **inward**:
- Outer layers depend on inner layers
- Inner layers are **independent** of outer layers
- Interfaces are defined in inner layers, implemented in outer layers

## Layer Structure

### 1. Domain Layer (`src/Domain/RssFeed/`)

The **innermost layer** containing pure business logic.

#### Entities
- **Feed** (`Entities/Feed.php`): Represents a user-subscribed RSS feed
- **FeedItem** (`Entities/FeedItem.php`): Represents a single entry from an RSS feed

**Key Characteristics:**
- Rich domain models with business logic
- No dependencies on frameworks or infrastructure
- Encapsulate business rules and invariants

#### Value Objects
- **FeedUrl** (`ValueObjects/FeedUrl.php`): Validates and encapsulates RSS feed URLs
- **FeedName** (`ValueObjects/FeedName.php`): Validates feed names
- **EntryId** (`ValueObjects/EntryId.php`): Represents unique RSS entry identifiers

**Key Characteristics:**
- Immutable
- Self-validating
- Equality based on value, not identity

#### Domain Services
- **RssFeedValidatorInterface** (`DomainServices/RssFeedValidatorInterface.php`): Interface for validating RSS feeds

#### Domain Events
- **FeedCreated**: Dispatched when a new feed is created
- **FeedProcessed**: Dispatched when a feed is successfully processed

#### Exceptions
- **FeedNotFoundException**: When a feed cannot be found
- **FeedAccessDeniedException**: When a user tries to access another user's feed
- **InvalidRssFeedException**: When RSS feed validation fails

### 2. Application Layer (`src/Application/RssFeed/`)

Orchestrates use cases and coordinates between domain and infrastructure.

#### Use Cases
- **CreateFeedUseCase**: Creates a new RSS feed subscription
- **ListUserFeedsUseCase**: Retrieves all feeds for a user
- **ListFeedItemsUseCase**: Retrieves feed items, optionally filtered by feed
- **ProcessFeedUseCase**: Fetches and parses an RSS feed, storing its items

**Key Characteristics:**
- Single responsibility per use case
- Coordinate domain entities and repositories
- Handle transaction boundaries
- Dispatch domain events

#### DTOs (Data Transfer Objects)
- **FeedDTO**: Data structure for feed information
- **FeedItemDTO**: Data structure for feed item information
- **CreateFeedDTO**: Input data for creating a feed
- **RssFeedEntryDTO**: Intermediate data structure for parsed RSS entries

**Key Characteristics:**
- Read-only (immutable)
- Plain data structures
- No business logic

#### Interfaces
- **FeedRepositoryInterface**: Contract for feed persistence
- **FeedItemRepositoryInterface**: Contract for feed item persistence
- **RssFeedParserInterface**: Contract for parsing RSS feeds

### 3. Infrastructure Layer (`src/Infrastructure/RssFeed/`)

Implements technical details and external integrations.

#### Repositories
- **EloquentFeedRepository**: Eloquent ORM implementation of `FeedRepositoryInterface`
- **EloquentFeedItemRepository**: Eloquent ORM implementation of `FeedItemRepositoryInterface`

**Responsibilities:**
- Map domain entities to/from database models
- Handle persistence concerns
- Implement query logic

#### Services
- **SimpleXmlRssFeedParser**: Parses RSS feeds using PHP's SimpleXML
- **HttpRssFeedValidator**: Validates RSS feeds via HTTP requests

**Responsibilities:**
- External service integration
- Technical implementation details
- Error handling for external calls

### 4. Presentation Layer (`app/Http/`)

Handles HTTP requests and responses.

#### Controllers
- **FeedController**: Handles feed-related HTTP requests

**Responsibilities:**
- Validate HTTP requests
- Delegate to use cases
- Format responses (Inertia.js)

#### Requests
- **CreateFeedRequest**: Validates feed creation input

#### Jobs
- **ProcessFeedJob**: Queued job for asynchronous feed processing

## Data Flow

### Creating a Feed

1. **HTTP Request** → `FeedController::store()`
2. **Request Validation** → `CreateFeedRequest`
3. **DTO Creation** → `CreateFeedDTO`
4. **Use Case Execution** → `CreateFeedUseCase::execute()`
5. **Domain Entity Creation** → `Feed::create()`
6. **Repository Persistence** → `EloquentFeedRepository::save()`
7. **Domain Event Dispatch** → `FeedCreated` event
8. **Event Listener** → `QueueFeedProcessing` (queues `ProcessFeedJob`)
9. **HTTP Response** → Redirect to feeds index

### Processing a Feed

1. **Job Dispatch** → `ProcessFeedJob`
2. **Use Case Execution** → `ProcessFeedUseCase::execute()`
3. **Repository Load** → `EloquentFeedRepository::findById()`
4. **RSS Parsing** → `SimpleXmlRssFeedParser::parseFeed()`
5. **Item Processing** → Create/update `FeedItem` entities
6. **Repository Persistence** → `EloquentFeedItemRepository::save()`
7. **Domain Event Dispatch** → `FeedProcessed` event

## Dependency Injection

### Service Provider Bindings

In `AppServiceProvider`, interfaces are bound to implementations:

```php
// Repositories
$this->app->bind(FeedRepositoryInterface::class, EloquentFeedRepository::class);
$this->app->bind(FeedItemRepositoryInterface::class, EloquentFeedItemRepository::class);

// Services
$this->app->bind(RssFeedParserInterface::class, SimpleXmlRssFeedParser::class);
$this->app->bind(RssFeedValidatorInterface::class, HttpRssFeedValidator::class);
```

This allows:
- Easy swapping of implementations (e.g., testing with mock repositories)
- Dependency inversion (application layer depends on interfaces)
- Framework-agnostic domain logic

### Constructor Injection

All dependencies are injected via constructors:

```php
public function __construct(
    private readonly FeedRepositoryInterface $feedRepository,
    private readonly ProcessFeedUseCase $processFeedUseCase
) {
}
```

Laravel's service container automatically resolves dependencies.

## Key Design Patterns

### 1. Repository Pattern
Abstracts data access, allowing the domain layer to remain persistence-agnostic.

### 2. Use Case Pattern
Encapsulates business operations in single-purpose classes.

### 3. Value Object Pattern
Ensures data integrity through immutability and validation.

### 4. Domain Events
Decouples components and enables event-driven architecture.

### 5. Dependency Inversion
High-level modules don't depend on low-level modules; both depend on abstractions.

### 6. Single Responsibility Principle
Each class has one reason to change.

## Testing Strategy

With this architecture, testing can be done at multiple levels:

1. **Domain Layer Tests**: Test entities and value objects in isolation
2. **Use Case Tests**: Test business logic with mocked repositories
3. **Integration Tests**: Test repository implementations with database
4. **Feature Tests**: Test HTTP endpoints end-to-end

## Further Improvements
If more time were available, I would consider the following enhancements:

*   **Caching Strategy**:
    *   Cache parsed feed entries to reduce external RSS calls and share results across workers
    *   Implement deterministic invalidation (e.g., flush cache on successful refresh or feed updates)
*   **Rate Limiting**:
    *   Enforce per-feed and global fetch intervals using Laravel's `RateLimiter` or Redis counters
    *   Track provider-specific policies (e.g., 1 request/min) to avoid being blocked by upstream sources
    *   Surface throttling/back-pressure indicators in the UI so users understand when a feed is temporarily paused
