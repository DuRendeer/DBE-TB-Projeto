# Petshop E-commerce API

## Informações do Projeto

**Disciplina:** Desenvolvimento Back-End com PHP e Laravel  
**Grupo:** Eduardo Sochodolak, Johann Matheus Pedroso da Silva, Alexsandro Lemos  
**Tema:** E-commerce de Petshop com Sistema de Agendamento de Banho e Tosa  
**Status:** Entrega 2 Concluída - API RESTful Completa

## Visão Geral

Este projeto consiste em uma API RESTful desenvolvida em Laravel para um e-commerce de produtos para pets, integrado com um sistema de agendamento de serviços como banho e tosa. O sistema permite o cadastro de usuários, pets, produtos e agendamentos de serviços.

## Funcionalidades Implementadas

- **Sistema de Autenticação:** Registro, login e logout com Laravel Breeze + Sanctum
- **API de Produtos:** CRUD completo com validação e relacionamentos
- **API de Agendamentos:** Sistema completo protegido por autenticação
- **Relacionamentos:** User, Pet, Product, Category, Service, Appointment
- **Validação Robusta:** Validação de dados em todas as operações
- **Testes Automatizados:** 3 testes unitários + 5 testes de feature
- **Segurança:** Middleware de autenticação e hash de senhas

## Modelagem de Dados

### Principais Entidades

#### 1. **Users (Usuários)**
- `id`: Chave primária
- `name`: Nome do usuário
- `email`: Email único
- `password`: Senha hash
- `timestamps`: Created/updated at

#### 2. **Pets (Animais de Estimação)**
- `id`: Chave primária
- `user_id`: FK para usuário proprietário
- `name`: Nome do pet
- `species`: Espécie (dog, cat, bird, fish, other)
- `breed`: Raça (opcional)
- `birth_date`: Data de nascimento
- `gender`: Gênero (male, female)
- `weight`: Peso em kg
- `notes`: Observações adicionais
- `photo`: Foto do pet
- `timestamps`

#### 3. **Categories (Categorias de Produtos)**
- `id`: Chave primária
- `name`: Nome da categoria
- `description`: Descrição
- `image`: Imagem da categoria
- `active`: Status ativo/inativo
- `timestamps`

#### 4. **Products (Produtos)**
- `id`: Chave primária
- `category_id`: FK para categoria
- `name`: Nome do produto
- `description`: Descrição detalhada
- `price`: Preço (decimal)
- `stock_quantity`: Quantidade em estoque
- `sku`: Código único do produto
- `images`: Array JSON com imagens
- `weight`: Peso do produto
- `dimensions`: Dimensões
- `active`: Status ativo/inativo
- `timestamps`

#### 5. **Services (Serviços)**
- `id`: Chave primária
- `name`: Nome do serviço
- `description`: Descrição
- `price`: Preço do serviço
- `duration_minutes`: Duração em minutos
- `active`: Status ativo/inativo
- `timestamps`

#### 6. **Appointments (Agendamentos)**
- `id`: Chave primária
- `user_id`: FK para usuário
- `pet_id`: FK para pet
- `service_id`: FK para serviço
- `scheduled_at`: Data/hora agendada
- `status`: Status (pending, confirmed, in_progress, completed, cancelled)
- `notes`: Observações
- `total_price`: Preço total
- `timestamps`

#### 7. **Orders (Pedidos)**
- `id`: Chave primária
- `user_id`: FK para usuário
- `order_number`: Número único do pedido
- `status`: Status do pedido
- `subtotal`: Subtotal
- `tax_amount`: Valor dos impostos
- `shipping_cost`: Custo de entrega
- `total_amount`: Valor total
- `shipping_address`: Endereço de entrega (JSON)
- `billing_address`: Endereço de cobrança (JSON)
- `payment_method`: Método de pagamento
- `payment_status`: Status do pagamento
- `shipped_at`: Data de envio
- `delivered_at`: Data de entrega
- `notes`: Observações
- `timestamps`

#### 8. **Order_Items (Itens do Pedido)**
- `id`: Chave primária
- `order_id`: FK para pedido
- `product_id`: FK para produto
- `quantity`: Quantidade
- `unit_price`: Preço unitário
- `total_price`: Preço total do item
- `timestamps`

#### 9. **Cart_Items (Itens do Carrinho)**
- `id`: Chave primária
- `user_id`: FK para usuário
- `product_id`: FK para produto
- `quantity`: Quantidade
- `timestamps`

## Relacionamentos

### Um para Muitos (1:N)
- **User → Pets:** Um usuário pode ter vários pets
- **User → Appointments:** Um usuário pode ter vários agendamentos
- **User → Orders:** Um usuário pode ter vários pedidos
- **User → Cart_Items:** Um usuário pode ter vários itens no carrinho
- **Category → Products:** Uma categoria pode ter vários produtos
- **Service → Appointments:** Um serviço pode ter vários agendamentos
- **Pet → Appointments:** Um pet pode ter vários agendamentos
- **Order → Order_Items:** Um pedido pode ter vários itens
- **Product → Order_Items:** Um produto pode estar em vários itens de pedido
- **Product → Cart_Items:** Um produto pode estar em vários carrinhos

## Justificativas das Escolhas de Modelagem

### 1. **Separação de Concerns**
- **Pets separados de Users:** Permite que um usuário tenha múltiplos pets com características específicas
- **Services separados:** Facilita a gestão de preços e duração dos serviços
- **Categories:** Organização lógica dos produtos para melhor navegação

### 2. **Flexibilidade de Dados**
- **JSON fields:** Uso de JSON para endereços e imagens permite flexibilidade sem criar tabelas adicionais
- **ENUM fields:** Para status e opções fixas, garantindo integridade de dados
- **Timestamps:** Auditoria temporal em todas as entidades

### 3. **Integridade Referencial**
- **Foreign Keys:** Relacionamentos bem definidos com cascade delete onde apropriado
- **Unique constraints:** SKU único para produtos, email único para usuários

### 4. **Escalabilidade**
- **Decimal precision:** Campos monetários com precisão adequada
- **Soft constraints:** Campos opcionais onde faz sentido (peso, raça, etc.)
- **Active flags:** Desativação lógica ao invés de exclusão física

## Configuração e Instalação

### Pré-requisitos
- PHP 8.2+
- Composer
- MySQL 8.0+
- Laravel 12.x

### Instalação
1. Clone o repositório
2. Execute `composer install`
3. Configure o arquivo `.env` com suas credenciais de banco
4. Execute `php artisan key:generate`
5. Execute `php artisan migrate`
6. Execute `php artisan db:seed`

### Testando a API
Após a instalação, você pode testar os endpoints:

#### Autenticação
- `POST /register` - Registro de usuário
- `POST /login` - Login de usuário
- `POST /logout` - Logout de usuário

#### Produtos (Público)
- `GET /api/products` - Listar produtos ativos
- `GET /api/products/{id}` - Visualizar produto específico
- `POST /api/products` - Criar produto
- `PUT /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Deletar produto

#### Agendamentos (Autenticado)
- `GET /api/appointments` - Listar agendamentos do usuário
- `POST /api/appointments` - Criar novo agendamento
- `GET /api/appointments/{id}` - Visualizar agendamento
- `PUT /api/appointments/{id}` - Atualizar agendamento
- `DELETE /api/appointments/{id}` - Cancelar agendamento

## Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── ProductController.php
│   ├── AppointmentController.php
│   └── HelloController.php
├── Models/
│   ├── User.php
│   ├── Pet.php
│   ├── Category.php
│   ├── Product.php
│   ├── Service.php
│   ├── Appointment.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── CartItem.php
database/
├── migrations/
├── seeders/
│   ├── CategorySeeder.php
│   ├── ServiceSeeder.php
│   └── DatabaseSeeder.php
└── factories/
    ├── ProductFactory.php
    ├── CategoryFactory.php
    ├── PetFactory.php
    ├── AppointmentFactory.php
    └── ServiceFactory.php
tests/
├── Unit/
│   ├── ProductTest.php
│   └── UserTest.php
└── Feature/
    ├── ProductApiTest.php
    └── AppointmentApiTest.php
```

## Implementações por Entrega

### Entrega 1 (15/08/2025) - Modelagem e Estrutura
- Repositório Git configurado
- 10 migrations criadas com relacionamentos
- 5 seeders funcionais com dados realistas
- Primeira rota e controller (HelloController)
- README com documentação completa da modelagem

### Entrega 2 (07/09/2025) - CRUD, Autenticação e Testes
- Laravel Breeze + Sanctum para autenticação
- ProductController com CRUD completo e validação
- AppointmentController com autenticação obrigatória
- Relacionamentos implementados em todos os Models
- 3 testes unitários (Product, User relationships)
- 5 testes de feature (APIs Product e Appointment)
- Factories para todos os Models de teste

## Testes Implementados

### Testes Unitários
- **ProductTest:** Relacionamentos, casting de dados, validações
- **UserTest:** Relacionamentos com pets e appointments, hash de senhas

### Testes de Feature
- **ProductApiTest:** CRUD completo da API de produtos
- **AppointmentApiTest:** Autenticação, criação e listagem de agendamentos

Para executar os testes:
```bash
php artisan test
```

---

**Histórico de Entregas:**
- **15/08/2025:** Entrega 1 - Modelagem e Estrutura Inicial Completa
- **08/09/2025:** Entrega 2 - API RESTful com Autenticação e Testes Completos
- **13/09/2025:** Apresentação Final - Demonstração Back-End
