# Petshop E-commerce API - Laravel

**Disciplina:** Desenvolvimento Back-End com PHP e Laravel
**Grupo:** Eduardo Sochodolak, Johann Matheus Pedroso da Silva, Alexsandro Lemos
**Tema:** E-commerce de Petshop com Sistema de Agendamento

---

## Visão Geral

API RESTful desenvolvida em Laravel para e-commerce de produtos pet com sistema integrado de agendamento de serviços (banho e tosa). O projeto aplica padrões de projeto avançados, princípios SOLID e arquitetura limpa.

**Status Atual:** Entrega - Padrões de Projeto e CQRS implementados

---

## Tecnologias

- **PHP 8.2+** | **Laravel 12.x** | **MySQL 8.0+**
- **Laravel Breeze + Sanctum** (Autenticação API)
- **PHPUnit** (Testes automatizados)

---

## Arquitetura e Padrões Implementados

### 1. Factory Method Pattern
**Localização:** `app/Factories/NotificationFactory.php`

Cria diferentes tipos de notificações (Email, SMS, Push) de forma centralizada.

```php
$notification = NotificationFactory::create('email');
$notification->send('user@gmail.com', 'Assunto', 'Mensagem');
```

**Benefícios:** Desacoplamento, fácil extensão, Open/Closed Principle

> **Nota Importante:** As notificações estão implementadas com logs simulados (`Log::info()`) para fins de demonstração e testes. Em produção, basta configurar o SMTP no `.env` e descomentar as linhas que utilizam `Mail::send()` do Laravel para enviar emails reais. Esta abordagem permite testar o padrão Factory sem dependências externas.

---

### 2. Strategy Pattern
**Localização:** `app/Strategies/` + `app/Services/PricingService.php`

Permite trocar algoritmos de precificação em runtime (regular, desconto por categoria, desconto por quantidade).

```php
$service = new PricingService(new BulkDiscountStrategy(5, 15));
$price = $service->calculate($product, 10); // 15% desconto para 10+ itens
```

**Benefícios:** Flexibilidade, testabilidade, Single Responsibility

---

### 3. CQRS Pattern
**Localização:** `app/Commands/`, `app/Queries/`, `app/Handlers/`

Separa operações de **escrita (Commands)** e **leitura (Queries)**.

```php
// Command (Escrita)
$command = new CreateAppointmentCommand($userId, $petId, $serviceId, ...);
$appointment = $handler->handle($command);

// Query (Leitura)
$query = new GetUserAppointmentsQuery($userId, status: 'pending');
$appointments = $handler->handle($query);
```

**Benefícios:** Separação de responsabilidades, otimização específica, auditoria

---

### 4. Repository Pattern
**Localização:** `app/Repositories/`

Abstrai acesso a dados, desacoplando lógica de negócio do ORM.

```php
class ProductController {
    public function __construct(
        private ProductRepositoryInterface $repository
    ) {}

    public function index() {
        return $this->repository->findActive();
    }
}
```

**Benefícios:** Dependency Inversion, testabilidade, flexibilidade

---

## Princípios SOLID Aplicados

| Princípio | Aplicação |
|-----------|-----------|
| **SRP** | Controllers coordenam, Handlers executam, Repositories acessam dados |
| **OCP** | Factory e Strategy permitem extensão sem modificação |
| **DIP** | Injeção de dependências via interfaces no `AppServiceProvider` |

```php
// AppServiceProvider.php
$this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
```

---

## Estrutura do Projeto

```
app/
├── Commands/                 # CQRS - Write operations
├── Queries/                  # CQRS - Read operations
├── Handlers/                 # CQRS - Executores
├── Factories/                # Factory Method
├── Strategies/               # Strategy Pattern
├── Repositories/             # Repository Pattern
├── Services/                 # Business Logic
│   ├── PricingService.php
│   └── Notifications/
├── Contracts/                # Interfaces
└── Http/Controllers/
    ├── ProductController.php
    ├── ProductControllerRefactored.php
    ├── AppointmentController.php
    └── AppointmentControllerRefactored.php

tests/
├── Unit/
│   ├── NotificationFactoryTest.php
│   ├── PricingStrategyTest.php
│   └── CommandValidationTest.php
└── Feature/
    ├── CQRSAppointmentTest.php
    ├── RepositoryPatternTest.php
    ├── ProductApiTest.php
    └── AppointmentApiTest.php
```

---

## Modelagem de Dados

### Entidades Principais

**Users** → **Pets** → **Appointments** ← **Services**
**Users** → **Orders** → **OrderItems** ← **Products** ← **Categories**
**Users** → **CartItems** ← **Products**

### Relacionamentos
- User **1:N** Pets, Appointments, Orders, CartItems
- Category **1:N** Products
- Service **1:N** Appointments
- Order **1:N** OrderItems

---

## Instalação e Uso

```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Migrar e popular banco
php artisan migrate
php artisan db:seed

# Rodar servidor
php artisan serve

# Rodar testes
php artisan test
```

---

## Testes

### Novos Testes (Entrega 3)
- `NotificationFactoryTest` - 6 testes (Factory)
- `PricingStrategyTest` - 6 testes (Strategy)
- `CommandValidationTest` - 6 testes (CQRS)
- `CQRSAppointmentTest` - 7 testes (CQRS integração)
- `RepositoryPatternTest` - 8 testes (Repository)

### Testes Anteriores
- Testes unitários: Product, User (6 testes)
- Testes de feature: Product API, Appointment API (6 testes)
- Testes de autenticação: Breeze (8 testes)

```bash
# Executar testes específicos
php artisan test --filter=NotificationFactoryTest
php artisan test --filter=PricingStrategyTest
php artisan test --filter=CQRSAppointmentTest
```

---

## Endpoints da API

### Autenticação
```
POST /api/register     - Registrar usuário
POST /api/login        - Login (retorna token)
```

### Products (público para leitura)
```
GET    /api/products           - Listar produtos ativos
GET    /api/products/{id}      - Ver produto
POST   /api/products           - Criar produto [AUTH]
PUT    /api/products/{id}      - Atualizar produto [AUTH]
DELETE /api/products/{id}      - Deletar produto [AUTH]
```

### Appointments (requer autenticação)
```
GET    /api/appointments       - Listar agendamentos do usuário
POST   /api/appointments       - Criar agendamento
GET    /api/appointments/{id}  - Ver agendamento
PUT    /api/appointments/{id}  - Atualizar agendamento
DELETE /api/appointments/{id}  - Cancelar agendamento
```

**Headers necessários:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Justificativas Técnicas

### Por que Factory Method?
Centraliza criação de notificações, facilitando adicionar novos canais (WhatsApp, Telegram) sem modificar código existente.

**Implementação Atual:**
- Notificações implementadas com **logs simulados** para demonstração
- Permite testar o padrão sem configurar servidor SMTP
- Em produção: configurar `.env` e descomentar `Mail::send()` do Laravel
- Não afeta a demonstração do padrão Factory

### Por que Strategy?
Permite trocar estratégias de preço dinamicamente (promoções, descontos sazonais) mantendo código limpo.

### Por que CQRS?
Separa leitura/escrita, permitindo otimizações específicas e melhor auditoria de operações.

### Por que Repository?
Desacopla controllers do Eloquent, facilitando testes com mocks e permitindo trocar ORM se necessário.

---

## Evolução do Projeto

| Entrega | Data | Foco | Status |
|---------|------|------|--------|
| **Entrega 1** | 15/08 | Modelagem + Migrations |
| **Entrega 2** | 08/09 | CRUD + Autenticação + Testes |
| **Entrega 3** | 07/11 | Padrões de Projeto + CQRS + SOLID |



