# Planejamento de Tarefas: Sistema de Gestão Financeira

## 📋 Visão Geral

Este documento detalha todas as tarefas necessárias para implementar o sistema de gestão financeira, organizadas por semanas em formato tabular.

**Duração Total:** 16 semanas (4 meses)  
**Metodologia:** Desenvolvimento incremental com entregas semanais  
**Total de Tarefas:** 254 tarefas  
**Total de Horas Estimadas:** ~1.288 horas

---

## 📊 Resumo por Fase

| Fase | Semanas | Tarefas | Horas Estimadas | Status |
|------|---------|---------|-----------------|--------|
| **Fase 1: Setup Inicial** | 1-2 | 45 | ~120h | ⏳ Pendente |
| **Fase 2: Domain Identity e Account** | 3-4 | 44 | ~166h | ⏳ Pendente |
| **Fase 3: Domain Transaction** | 5-6 | 36 | ~172h | ⏳ Pendente |
| **Fase 4: Domain Planning** | 7-8 | 20 | ~100h | ⏳ Pendente |
| **Fase 5: Frontend + Integração** | 9-10 | 28 | ~168h | ⏳ Pendente |
| **Fase 6: Segurança e Compliance** | 11-12 | 28 | ~180h | ⏳ Pendente |
| **Fase 7: Observabilidade e Performance** | 13-14 | 24 | ~150h | ⏳ Pendente |
| **Fase 8: Deploy e Infraestrutura** | 15-16 | 25 | ~200h | ⏳ Pendente |
| **TOTAL** | **16** | **254** | **~1.288h** | **0%** |

---

## 📅 Semana 1: Infraestrutura Base

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 1.1 | Instalar Laravel via Composer | 30 min | 🔴 Alta | Nenhuma | Projeto Laravel criado | ✅ |
| 1.2 | Configurar `.env` e variáveis de ambiente | 1h | 🔴 Alta | Projeto Laravel criado | Arquivo `.env` configurado | ✅ |
| 1.3 | Configurar namespace e autoload | 30 min | 🟡 Média | Projeto Laravel criado | `composer.json` atualizado | ✅ |
| 1.4 | Criar `docker-compose.yml` | 2h | 🔴 Alta | Nenhuma | Arquivo `docker-compose.yml` criado | ⬜ |
| 1.5 | Criar Dockerfile para PHP-FPM | 1h | 🔴 Alta | Nenhuma | `docker/Dockerfile` criado | ⬜ |
| 1.6 | Configurar Nginx | 1h | 🔴 Alta | Dockerfile criado | `docker/nginx/default.conf` configurado | ⬜ |
| 1.7 | Configurar PostgreSQL no Docker | 30 min | 🔴 Alta | docker-compose.yml | Container PostgreSQL funcionando | ⬜ |
| 1.8 | Configurar Redis no Docker | 30 min | 🔴 Alta | docker-compose.yml | Container Redis funcionando | ⬜ |
| 1.9 | Testar ambiente Docker completo | 1h | 🔴 Alta | Todos os containers configurados | Ambiente rodando localmente | ⬜ |
| 1.10 | Criar estrutura de pastas Domain | 2h | 🔴 Alta | Projeto Laravel criado | Estrutura Domain criada | ⬜ |
| 1.11 | Criar estrutura de pastas Application | 1h | 🔴 Alta | Estrutura Domain criada | Estrutura Application criada | ⬜ |
| 1.12 | Criar estrutura de pastas Infrastructure | 1h | 🔴 Alta | Estrutura Application criada | Estrutura Infrastructure criada | ⬜ |
| 1.13 | Criar estrutura de pastas Interfaces | 1h | 🔴 Alta | Estrutura Infrastructure criada | Estrutura Interfaces criada | ⬜ |

**Total Semana 1:** 13 tarefas | ~13h | 🔴 Alta: 12 | 🟡 Média: 1

---

## 📅 Semana 2: Ferramentas e Configurações

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 2.1 | Instalar PHPStan | 30 min | 🔴 Alta | Projeto Laravel | PHPStan instalado e configurado | ⬜ |
| 2.2 | Configurar regras PHPStan | 1h | 🟡 Média | PHPStan instalado | `phpstan.neon` configurado | ⬜ |
| 2.3 | Instalar e configurar Laravel Pint | 30 min | 🔴 Alta | Projeto Laravel | Pint instalado e configurado | ⬜ |
| 2.4 | Criar regras de formatação | 30 min | 🟡 Média | Pint instalado | `pint.json` configurado | ⬜ |
| 2.5 | Instalar Pest | 30 min | 🔴 Alta | Projeto Laravel | Pest instalado | ⬜ |
| 2.6 | Configurar Pest | 1h | 🔴 Alta | Pest instalado | `tests/Pest.php` configurado | ⬜ |
| 2.7 | Criar testes de exemplo | 1h | 🟡 Média | Pest configurado | Teste de exemplo funcionando | ⬜ |
| 2.8 | Criar workflow GitHub Actions para testes | 2h | 🔴 Alta | Repositório Git criado | `.github/workflows/ci.yml` criado | ⬜ |
| 2.9 | Configurar serviços no CI (PostgreSQL, Redis) | 1h | 🔴 Alta | Workflow criado | CI rodando com serviços | ⬜ |
| 2.10 | Adicionar job de lint (Pint) | 30 min | 🟡 Média | CI básico funcionando | Job de lint no CI | ⬜ |
| 2.11 | Adicionar job de análise estática (PHPStan) | 30 min | 🟡 Média | CI básico funcionando | Job de PHPStan no CI | ⬜ |
| 2.12 | Instalar Laravel Pennant | 30 min | 🔴 Alta | Projeto Laravel | Pennant instalado | ⬜ |
| 2.13 | Publicar migrations do Pennant | 15 min | 🔴 Alta | Pennant instalado | Migrations publicadas | ⬜ |
| 2.14 | Criar FeatureFlagServiceProvider | 1h | 🔴 Alta | Pennant instalado | Provider criado | ⬜ |
| 2.15 | Definir features iniciais | 1h | 🟡 Média | Provider criado | Features definidas | ⬜ |
| 2.16 | Configurar canais de log | 1h | 🔴 Alta | Projeto Laravel | `config/logging.php` configurado | ⬜ |
| 2.17 | Criar StructuredLogger | 2h | 🔴 Alta | Canais configurados | Classe StructuredLogger criada | ⬜ |
| 2.18 | Configurar rotação de logs | 30 min | 🟡 Média | Logging configurado | Rotação configurada | ⬜ |
| 2.19 | Criar HealthController | 1h | 🔴 Alta | Projeto Laravel | Controller criado | ⬜ |
| 2.20 | Implementar check de database | 30 min | 🔴 Alta | HealthController criado | Check de DB funcionando | ⬜ |
| 2.21 | Implementar check de Redis | 30 min | 🔴 Alta | HealthController criado | Check de Redis funcionando | ⬜ |
| 2.22 | Criar rota `/health` | 15 min | 🔴 Alta | HealthController criado | Rota funcionando | ⬜ |
| 2.23 | Criar serviço de backup no docker-compose | 1h | 🔴 Alta | Docker configurado | Serviço de backup no compose | ⬜ |
| 2.24 | Criar script de backup | 1h | 🔴 Alta | Serviço criado | Script `backup.sh` criado | ⬜ |
| 2.25 | Configurar retenção de backups | 30 min | 🟡 Média | Script criado | Retenção configurada | ⬜ |

**Total Semana 2:** 25 tarefas | ~18h | 🔴 Alta: 18 | 🟡 Média: 7

---

## 📅 Semana 3: Value Objects e Entidades Base

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 3.1 | Implementar Value Object Money | 3h | 🔴 Alta | Estrutura Domain criada | Classe `Money.php` completa | ⬜ |
| 3.2 | Implementar Value Object Document (CPF/CNPJ) | 4h | 🔴 Alta | Estrutura Domain criada | Classe `Document.php` completa | ⬜ |
| 3.3 | Implementar Value Object Email | 1h | 🔴 Alta | Estrutura Domain criada | Classe `Email.php` completa | ⬜ |
| 3.4 | Implementar Value Object Uuid | 1h | 🔴 Alta | Estrutura Domain criada | Classe `Uuid.php` completa | ⬜ |
| 3.5 | Criar testes unitários para Value Objects | 4h | 🔴 Alta | Value Objects implementados | Testes criados e passando | ⬜ |
| 3.6 | Implementar entidade User | 2h | 🔴 Alta | Value Objects criados | Classe `User.php` completa | ⬜ |
| 3.7 | Implementar entidade Organization | 3h | 🔴 Alta | Value Objects criados | Classe `Organization.php` completa | ⬜ |
| 3.8 | Implementar entidade Account | 3h | 🔴 Alta | Value Objects criados | Classe `Account.php` completa | ⬜ |
| 3.9 | Implementar entidade AccountType | 1h | 🔴 Alta | Estrutura Domain criada | Classe `AccountType.php` completa | ⬜ |
| 3.10 | Criar migration de organizations | 1h | 🔴 Alta | Entidade Organization criada | Migration criada | ⬜ |
| 3.11 | Criar migration de users | 1h | 🔴 Alta | Entidade User criada | Migration criada | ⬜ |
| 3.12 | Criar migration de account_types | 1h | 🔴 Alta | Entidade AccountType criada | Migration criada | ⬜ |
| 3.13 | Criar migration de accounts (com campos de empréstimo) | 1h | 🔴 Alta | Entidade Account criada | Migration criada | ⬜ |
| 3.14 | Criar seeder de account_types (incluindo tipo loan) | 1h | 🟡 Média | Migration criada | Seeder criado | ⬜ |
| 3.15 | Executar migrations e seeders | 30 min | 🔴 Alta | Todas as migrations criadas | Banco de dados populado | ⬜ |

**Total Semana 3:** 15 tarefas | ~27h | 🔴 Alta: 14 | 🟡 Média: 1

---

## 📅 Semana 4: Autenticação e Endpoints

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 4.1 | Instalar Laravel Sanctum | 30 min | 🔴 Alta | Projeto Laravel | Sanctum instalado | ⬜ |
| 4.2 | Publicar configuração do Sanctum | 15 min | 🔴 Alta | Sanctum instalado | Config publicado | ⬜ |
| 4.3 | Criar AuthController | 3h | 🔴 Alta | Sanctum instalado | Controller criado | ⬜ |
| 4.4 | Criar rotas de autenticação | 30 min | 🔴 Alta | AuthController criado | Rotas criadas | ⬜ |
| 4.5 | Criar requests de validação | 1h | 🔴 Alta | AuthController criado | Form Requests criados | ⬜ |
| 4.6 | Configurar rate limiting global | 30 min | 🔴 Alta | Projeto Laravel | Rate limiting configurado | ⬜ |
| 4.7 | Configurar rate limiting por endpoint | 1h | 🔴 Alta | Rate limiting global | Limites por endpoint configurados | ⬜ |
| 4.8 | Criar middleware customizado (se necessário) | 1h | 🟡 Média | Rate limiting configurado | Middleware criado | ⬜ |
| 4.9 | Criar regra de validação StrongPassword | 1h | 🔴 Alta | Projeto Laravel | Regra criada | ⬜ |
| 4.10 | Aplicar regra no registro | 30 min | 🔴 Alta | Regra criada | Validação aplicada | ⬜ |
| 4.11 | Criar endpoint de alteração de senha | 1h | 🟡 Média | Autenticação funcionando | Endpoint criado | ⬜ |
| 4.12 | Criar AccountController | 2h | 🔴 Alta | Entidade Account criada | Controller criado | ⬜ |
| 4.13 | Implementar listagem de contas | 1h | 🔴 Alta | AccountController criado | Endpoint GET /accounts | ⬜ |
| 4.14 | Implementar criação de conta | 2h | 🔴 Alta | AccountController criado | Endpoint POST /accounts | ⬜ |
| 4.15 | Implementar atualização de conta | 1h | 🔴 Alta | AccountController criado | Endpoint PUT /accounts/{id} | ⬜ |
| 4.16 | Implementar exclusão de conta | 1h | 🔴 Alta | AccountController criado | Endpoint DELETE /accounts/{id} | ⬜ |
| 4.17 | Implementar consulta de saldo | 2h | 🔴 Alta | AccountController criado | Endpoint GET /accounts/{id}/balance | ⬜ |
| 4.18 | Criar AccountResource | 1h | 🟡 Média | Endpoints criados | Resource criado | ⬜ |
| 4.19 | Implementar endpoint POST /accounts/{id}/lend | 2h | 🔴 Alta | AccountController criado | Endpoint de empréstimo criado | ⬜ |
| 4.20 | Implementar endpoint GET /accounts/{id}/loans | 1h | 🔴 Alta | AccountController criado | Endpoint de listagem criado | ⬜ |
| 4.21 | Implementar endpoint POST /accounts/{id}/repay | 2h | 🔴 Alta | AccountController criado | Endpoint de pagamento criado | ⬜ |
| 4.22 | Criar testes para entidade User | 2h | 🔴 Alta | Entidade User criada | Testes criados | ⬜ |
| 4.23 | Criar testes para entidade Organization | 2h | 🔴 Alta | Entidade Organization criada | Testes criados | ⬜ |
| 4.24 | Criar testes para entidade Account | 2h | 🔴 Alta | Entidade Account criada | Testes criados | ⬜ |
| 4.25 | Criar testes para endpoints de empréstimo | 2h | 🔴 Alta | Endpoints criados | Testes criados | ⬜ |
| 4.26 | Criar migration para habilitar RLS | 2h | 🔴 Alta | Migrations criadas | Migration de RLS criada | ⬜ |
| 4.27 | Criar políticas RLS para accounts | 1h | 🔴 Alta | RLS habilitado | Políticas criadas | ⬜ |
| 4.28 | Criar TenantMiddleware | 2h | 🔴 Alta | RLS configurado | Middleware criado | ⬜ |
| 4.29 | Testar isolamento de dados | 1h | 🔴 Alta | Middleware criado | Testes de isolamento passando | ⬜ |

**Total Semana 4:** 29 tarefas | ~36h | 🔴 Alta: 26 | 🟡 Média: 3

---

## 📅 Semana 5: Entidade Transaction e Category

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 5.1 | Criar entidade Transaction | 4h | 🔴 Alta | Value Objects criados | Classe `Transaction.php` completa | ⬜ |
| 5.2 | Criar Value Object TransactionType | 1h | 🔴 Alta | Estrutura Domain criada | Classe `TransactionType.php` criada | ⬜ |
| 5.3 | Criar Domain Events | 2h | 🔴 Alta | Entidade Transaction criada | Events criados | ⬜ |
| 5.4 | Criar entidade Category | 3h | 🔴 Alta | Estrutura Domain criada | Classe `Category.php` completa | ⬜ |
| 5.5 | Criar migration de categories | 1h | 🔴 Alta | Entidade Category criada | Migration criada | ⬜ |
| 5.6 | Implementar hierarquia (parent_id) | 2h | 🔴 Alta | Entidade Category criada | Hierarquia funcionando | ⬜ |
| 5.7 | Criar CreateQuickTransactionCommand | 1h | 🔴 Alta | Entidade Transaction criada | Command criado | ⬜ |
| 5.8 | Criar CreateQuickTransactionHandler | 2h | 🔴 Alta | Command criado | Handler criado | ⬜ |
| 5.9 | Criar endpoint POST /transactions/quick | 1h | 🔴 Alta | Handler criado | Endpoint criado | ⬜ |
| 5.10 | Implementar método confirm() na entidade | 1h | 🔴 Alta | Entidade Transaction criada | Método implementado | ⬜ |
| 5.11 | Criar ConfirmTransactionCommand | 1h | 🔴 Alta | Método confirm() criado | Command criado | ⬜ |
| 5.12 | Criar endpoint POST /transactions/{id}/confirm | 1h | 🔴 Alta | Command criado | Endpoint criado | ⬜ |
| 5.13 | Criar DefaultCategoriesSeeder | 3h | 🔴 Alta | Migration de categories | Seeder criado | ⬜ |
| 5.14 | Executar seeder | 15 min | 🔴 Alta | Seeder criado | Categorias no banco | ⬜ |

**Total Semana 5:** 14 tarefas | ~24h | 🔴 Alta: 14 | 🟡 Média: 0

---

## 📅 Semana 6: Testes e Tratamento de Erros

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 6.1 | Criar TransactionRepositoryTest | 3h | 🔴 Alta | Repository criado | Testes criados | ⬜ |
| 6.2 | Criar testes para GlobalBalanceCalculator | 2h | 🔴 Alta | Calculator criado | Testes criados | ⬜ |
| 6.3 | Criar testes para Domain Events | 2h | 🟡 Média | Events criados | Testes criados | ⬜ |
| 6.4 | Criar TransactionApiTest | 4h | 🔴 Alta | Endpoints criados | Testes criados | ⬜ |
| 6.5 | Criar CategoryApiTest | 2h | 🟡 Média | Endpoints criados | Testes criados | ⬜ |
| 6.6 | Testar autenticação nas rotas | 1h | 🔴 Alta | Testes criados | Testes de auth passando | ⬜ |
| 6.7 | Criar Exception Handler customizado | 2h | 🔴 Alta | Projeto Laravel | Handler criado | ⬜ |
| 6.8 | Criar exceptions de domínio | 2h | 🔴 Alta | Estrutura Domain criada | Exceptions criadas | ⬜ |
| 6.9 | Implementar respostas de erro padronizadas | 2h | 🔴 Alta | Handler criado | Respostas padronizadas | ⬜ |
| 6.10 | Criar testes para tratamento de erros | 2h | 🟡 Média | Handler criado | Testes criados | ⬜ |
| 6.11 | Criar OfxParser service | 3h | 🔴 Alta | Estrutura Infrastructure | Service criado | ⬜ |
| 6.12 | Criar ImportTransactionsCommand | 1h | 🔴 Alta | OfxParser criado | Command criado | ⬜ |
| 6.13 | Criar ImportTransactionsHandler | 3h | 🔴 Alta | Command criado | Handler criado | ⬜ |
| 6.14 | Implementar detecção de duplicatas | 2h | 🔴 Alta | Handler criado | Detecção funcionando | ⬜ |
| 6.15 | Criar TransactionImportController | 2h | 🔴 Alta | Handler criado | Controller criado | ⬜ |
| 6.16 | Criar endpoint POST /transactions/import | 1h | 🔴 Alta | Controller criado | Endpoint criado | ⬜ |
| 6.17 | Criar ImportTransactionsJob (processamento assíncrono) | 2h | 🔴 Alta | Handler criado | Job criado | ⬜ |
| 6.18 | Criar testes para importação OFX | 3h | 🔴 Alta | Endpoint criado | Testes criados | ⬜ |

**Total Semana 6:** 18 tarefas | ~32h | 🔴 Alta: 14 | 🟡 Média: 4

---

## 📅 Semana 7: Entidade Goal e Contribuições

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 7.1 | Criar entidade Goal | 4h | 🔴 Alta | Value Objects criados | Classe `Goal.php` completa | ⬜ |
| 7.2 | Criar Value Object GoalStatus | 1h | 🔴 Alta | Estrutura Domain criada | Classe `GoalStatus.php` criada | ⬜ |
| 7.3 | Criar migration de goals | 1h | 🔴 Alta | Entidade Goal criada | Migration criada | ⬜ |
| 7.4 | Criar Domain Events para Goal | 2h | 🟡 Média | Entidade Goal criada | Events criados | ⬜ |
| 7.5 | Implementar método contribute() | 2h | 🔴 Alta | Entidade Goal criada | Método implementado | ⬜ |
| 7.6 | Implementar método withdraw() | 2h | 🔴 Alta | Entidade Goal criada | Método implementado | ⬜ |
| 7.7 | Criar ContributeToGoalCommand | 1h | 🔴 Alta | Método contribute() criado | Command criado | ⬜ |
| 7.8 | Criar endpoint POST /goals/{id}/contribute | 1h | 🔴 Alta | Command criado | Endpoint criado | ⬜ |
| 7.9 | Implementar método progressPercentage() | 1h | 🔴 Alta | Entidade Goal criada | Método implementado | ⬜ |
| 7.10 | Implementar método remainingAmount() | 1h | 🔴 Alta | Entidade Goal criada | Método implementado | ⬜ |
| 7.11 | Criar GoalProgressCalculator service | 2h | 🟡 Média | Métodos criados | Service criado | ⬜ |

**Total Semana 7:** 11 tarefas | ~18h | 🔴 Alta: 9 | 🟡 Média: 2

---

## 📅 Semana 8: Notificações e Testes

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 8.1 | Criar notificação GoalCompleted | 1h | 🟡 Média | Entidade Goal criada | Notificação criada | ⬜ |
| 8.2 | Criar listener para GoalCompleted | 1h | 🟡 Média | Notificação criada | Listener criado | ⬜ |
| 8.3 | Configurar canais de notificação | 1h | 🟡 Média | Listener criado | Canais configurados | ⬜ |
| 8.4 | Criar teste E2E de fluxo de transação | 3h | 🔴 Alta | Endpoints criados | Teste criado | ⬜ |
| 8.5 | Criar teste E2E de fluxo de meta | 3h | 🔴 Alta | Endpoints criados | Teste criado | ⬜ |
| 8.6 | Configurar ambiente de testes E2E | 2h | 🔴 Alta | Testes criados | Ambiente configurado | ⬜ |
| 8.7 | Configurar retry em CalculateGlobalBalanceJob | 1h | 🔴 Alta | Job criado | Retry configurado | ⬜ |
| 8.8 | Implementar método failed() nos jobs | 2h | 🔴 Alta | Jobs criados | Método implementado | ⬜ |
| 8.9 | Criar dead letter queue | 2h | 🟡 Média | Método failed() criado | DLQ configurado | ⬜ |

**Total Semana 8:** 9 tarefas | ~16h | 🔴 Alta: 5 | 🟡 Média: 4

---

## 📅 Semana 9: Setup Frontend e Autenticação

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 9.1 | Criar projeto Nuxt 3 com Vue 3 | 2h | 🔴 Alta | Nenhuma | Projeto criado | ⬜ |
| 9.2 | Configurar TypeScript no Nuxt | 1h | 🔴 Alta | Projeto criado | TypeScript configurado | ⬜ |
| 9.3 | Instalar e configurar Tailwind CSS | 1h | 🔴 Alta | Projeto criado | Tailwind configurado | ⬜ |
| 9.4 | Instalar e configurar shadcn-vue | 2h | 🔴 Alta | Tailwind configurado | shadcn-vue configurado | ⬜ |
| 9.5 | Adicionar componentes base do shadcn-vue | 2h | 🔴 Alta | shadcn-vue configurado | Componentes base instalados | ⬜ |
| 9.6 | Configurar roteamento do Nuxt | 1h | 🔴 Alta | Projeto criado | Roteamento configurado | ⬜ |
| 9.7 | Configurar gerenciamento de estado (Pinia) | 2h | 🔴 Alta | Projeto criado | Estado configurado | ⬜ |
| 9.8 | Configurar cliente HTTP ($fetch/Axios) | 1h | 🔴 Alta | Projeto criado | Cliente configurado | ⬜ |
| 9.9 | Criar página de login (com shadcn-vue) | 2h | 🔴 Alta | Componentes base instalados | Página criada | ⬜ |
| 9.10 | Criar página de registro (com shadcn-vue) | 2h | 🔴 Alta | Componentes base instalados | Página criada | ⬜ |
| 9.11 | Implementar gerenciamento de token | 2h | 🔴 Alta | Autenticação funcionando | Token gerenciado | ⬜ |
| 9.12 | Criar middleware de autenticação (Nuxt) | 1h | 🔴 Alta | Token gerenciado | Middleware criado | ⬜ |
| 9.13 | Implementar refresh token | 2h | 🟡 Média | Autenticação funcionando | Refresh implementado | ⬜ |
| 9.14 | Criar layout base (com shadcn-vue) | 2h | 🔴 Alta | Componentes base instalados | Layout criado | ⬜ |
| 9.15 | Criar componente de saldo global | 2h | 🔴 Alta | API funcionando | Componente criado | ⬜ |
| 9.16 | Criar gráfico de receitas vs despesas | 3h | 🟡 Média | API funcionando | Gráfico criado | ⬜ |
| 9.17 | Criar lista de transações recentes | 2h | 🔴 Alta | API funcionando | Lista criada | ⬜ |

**Total Semana 9:** 17 tarefas | ~28h | 🔴 Alta: 13 | 🟡 Média: 4

---

## 📅 Semana 10: Formulários e Listagens

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 10.1 | Criar componente de entrada rápida (com shadcn-vue) | 3h | 🔴 Alta | API funcionando | Componente criado | ⬜ |
| 10.2 | Implementar seleção de categoria (Select shadcn-vue) | 2h | 🔴 Alta | Componente criado | Seleção funcionando | ⬜ |
| 10.3 | Implementar seleção de conta (Select shadcn-vue) | 1h | 🔴 Alta | Componente criado | Seleção funcionando | ⬜ |
| 10.4 | Adicionar validação no frontend (com shadcn-vue) | 2h | 🔴 Alta | Formulário criado | Validação funcionando | ⬜ |
| 10.5 | Criar componente de listagem | 3h | 🔴 Alta | API funcionando | Componente criado | ⬜ |
| 10.6 | Implementar paginação | 2h | 🔴 Alta | Listagem criada | Paginação funcionando | ⬜ |
| 10.7 | Implementar filtros | 3h | 🟡 Média | Listagem criada | Filtros funcionando | ⬜ |
| 10.8 | Implementar busca | 2h | 🟡 Média | Listagem criada | Busca funcionando | ⬜ |
| 10.9 | Criar página de listagem de metas | 2h | 🔴 Alta | API funcionando | Página criada | ⬜ |
| 10.10 | Criar formulário de criação de meta | 2h | 🔴 Alta | API funcionando | Formulário criado | ⬜ |
| 10.11 | Criar componente de progresso | 2h | 🔴 Alta | API funcionando | Componente criado | ⬜ |
| 10.12 | Implementar contribuição para meta | 2h | 🔴 Alta | Componente criado | Contribuição funcionando | ⬜ |
| 10.13 | Gerar documentação OpenAPI | 2h | 🟡 Média | Endpoints criados | Documentação gerada | ⬜ |
| 10.14 | Integrar Swagger UI no frontend | 1h | 🟢 Baixa | Documentação gerada | Swagger integrado | ⬜ |

**Total Semana 10:** 14 tarefas | ~27h | 🔴 Alta: 10 | 🟡 Média: 3 | 🟢 Baixa: 1

---

## 📅 Semana 11: Segurança Avançada

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 11.1 | Instalar Laravel Fortify | 30 min | 🔴 Alta | Projeto Laravel | Fortify instalado | ⬜ |
| 11.2 | Configurar 2FA | 3h | 🔴 Alta | Fortify instalado | 2FA configurado | ⬜ |
| 11.3 | Criar endpoints de 2FA | 2h | 🔴 Alta | 2FA configurado | Endpoints criados | ⬜ |
| 11.4 | Criar middleware para rotas sensíveis | 1h | 🔴 Alta | 2FA funcionando | Middleware criado | ⬜ |
| 11.5 | Testar fluxo completo de 2FA | 2h | 🔴 Alta | Middleware criado | Testes passando | ⬜ |
| 11.6 | Instalar Laravel Passport | 30 min | 🟡 Média | Projeto Laravel | Passport instalado | ⬜ |
| 11.7 | Configurar Passport | 2h | 🟡 Média | Passport instalado | Passport configurado | ⬜ |
| 11.8 | Criar OAuthController | 2h | 🟡 Média | Passport configurado | Controller criado | ⬜ |
| 11.9 | Criar rotas OAuth | 1h | 🟡 Média | Controller criado | Rotas criadas | ⬜ |
| 11.10 | Criar SecurityLogger | 2h | 🔴 Alta | Logging configurado | Logger criado | ⬜ |
| 11.11 | Implementar log de tentativas de login | 1h | 🔴 Alta | Logger criado | Log implementado | ⬜ |
| 11.12 | Implementar log de ações sensíveis | 2h | 🔴 Alta | Logger criado | Log implementado | ⬜ |
| 11.13 | Criar endpoint de visualização de logs | 2h | 🟢 Baixa | Logs implementados | Endpoint criado | ⬜ |

**Total Semana 11:** 13 tarefas | ~22h | 🔴 Alta: 8 | 🟡 Média: 4 | 🟢 Baixa: 1

---

## 📅 Semana 12: Compliance LGPD

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 12.1 | Criar migration para campos de consentimento | 1h | 🔴 Alta | Migration de users | Migration criada | ⬜ |
| 12.2 | Implementar aceite de termos | 2h | 🔴 Alta | Migration criada | Aceite implementado | ⬜ |
| 12.3 | Implementar aceite de política de privacidade | 1h | 🔴 Alta | Migration criada | Aceite implementado | ⬜ |
| 12.4 | Criar página de termos | 1h | 🟡 Média | Frontend criado | Página criada | ⬜ |
| 12.5 | Criar página de política de privacidade | 1h | 🟡 Média | Frontend criado | Página criada | ⬜ |
| 12.6 | Criar ExportUserDataCommand | 3h | 🔴 Alta | Entidades criadas | Command criado | ⬜ |
| 12.7 | Criar endpoint GET /user/export-data | 1h | 🔴 Alta | Command criado | Endpoint criado | ⬜ |
| 12.8 | Implementar geração de arquivo JSON | 2h | 🔴 Alta | Command criado | Geração funcionando | ⬜ |
| 12.9 | Implementar download do arquivo | 1h | 🔴 Alta | Geração funcionando | Download funcionando | ⬜ |
| 12.10 | Criar DeleteUserDataCommand | 4h | 🔴 Alta | Entidades criadas | Command criado | ⬜ |
| 12.11 | Implementar anonimização de dados | 2h | 🔴 Alta | Command criado | Anonimização funcionando | ⬜ |
| 12.12 | Criar endpoint DELETE /user/data | 1h | 🔴 Alta | Command criado | Endpoint criado | ⬜ |
| 12.13 | Implementar confirmação de exclusão | 1h | 🔴 Alta | Endpoint criado | Confirmação funcionando | ⬜ |
| 12.14 | Executar scan de vulnerabilidades | 2h | 🔴 Alta | Aplicação funcionando | Scan executado | ⬜ |
| 12.15 | Corrigir vulnerabilidades encontradas | 4h | 🔴 Alta | Scan executado | Vulnerabilidades corrigidas | ⬜ |
| 12.16 | Testar proteção CSRF | 1h | 🔴 Alta | Proteção implementada | Testes passando | ⬜ |
| 12.17 | Testar proteção XSS | 1h | 🔴 Alta | Proteção implementada | Testes passando | ⬜ |

**Total Semana 12:** 17 tarefas | ~28h | 🔴 Alta: 14 | 🟡 Média: 2 | 🟢 Baixa: 1

---

## 📅 Semana 13: Observabilidade

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 13.1 | Instalar OpenTelemetry PHP | 1h | 🟡 Média | Projeto Laravel | OpenTelemetry instalado | ⬜ |
| 13.2 | Configurar tracing | 2h | 🟡 Média | OpenTelemetry instalado | Tracing configurado | ⬜ |
| 13.3 | Criar TracingMiddleware | 2h | 🟡 Média | Tracing configurado | Middleware criado | ⬜ |
| 13.4 | Testar traces | 1h | 🟡 Média | Middleware criado | Traces funcionando | ⬜ |
| 13.5 | Instalar Prometheus/StatsD | 1h | 🟡 Média | Projeto Laravel | Ferramenta instalada | ⬜ |
| 13.6 | Criar MetricsCollector | 2h | 🟡 Média | Ferramenta instalada | Collector criado | ⬜ |
| 13.7 | Implementar métricas de transações | 2h | 🟡 Média | Collector criado | Métricas funcionando | ⬜ |
| 13.8 | Criar dashboard básico | 3h | 🟢 Baixa | Métricas funcionando | Dashboard criado | ⬜ |
| 13.9 | Instalar Sentry Laravel | 30 min | 🔴 Alta | Projeto Laravel | Sentry instalado | ⬜ |
| 13.10 | Configurar Sentry | 1h | 🔴 Alta | Sentry instalado | Sentry configurado | ⬜ |
| 13.11 | Adicionar contexto customizado | 1h | 🟡 Média | Sentry configurado | Contexto adicionado | ⬜ |
| 13.12 | Testar envio de erros | 1h | 🔴 Alta | Sentry configurado | Testes passando | ⬜ |

**Total Semana 13:** 12 tarefas | ~17h | 🔴 Alta: 3 | 🟡 Média: 7 | 🟢 Baixa: 2

---

## 📅 Semana 14: Performance

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 14.1 | Analisar queries lentas | 2h | 🔴 Alta | Aplicação funcionando | Análise completa | ⬜ |
| 14.2 | Adicionar índices faltantes | 2h | 🔴 Alta | Análise feita | Índices adicionados | ⬜ |
| 14.3 | Otimizar queries N+1 | 3h | 🔴 Alta | Análise feita | Queries otimizadas | ⬜ |
| 14.4 | Implementar eager loading | 2h | 🔴 Alta | Queries identificadas | Eager loading implementado | ⬜ |
| 14.5 | Criar CachedGlobalBalanceCalculator | 2h | 🔴 Alta | Calculator criado | Cached calculator criado | ⬜ |
| 14.6 | Implementar invalidação de cache | 2h | 🔴 Alta | Cached calculator criado | Invalidação funcionando | ⬜ |
| 14.7 | Criar listener para invalidação | 2h | 🔴 Alta | Invalidação criada | Listener criado | ⬜ |
| 14.8 | Criar comando de cache warming | 1h | 🟡 Média | Cached calculator criado | Comando criado | ⬜ |
| 14.9 | Criar estrutura de Read Models | 1h | 🟡 Média | Estrutura Infrastructure | Estrutura criada | ⬜ |
| 14.10 | Criar CategorySummaryReadModel | 2h | 🟡 Média | Estrutura criada | Read Model criado | ⬜ |
| 14.11 | Criar materialized view | 2h | 🟡 Média | Read Model criado | View criada | ⬜ |
| 14.12 | Criar projector para atualizar Read Model | 3h | 🟡 Média | Read Model criado | Projector criado | ⬜ |
| 14.13 | Adicionar PgBouncer ao docker-compose | 1h | 🟡 Média | Docker configurado | PgBouncer no compose | ⬜ |
| 14.14 | Configurar Laravel para usar PgBouncer | 1h | 🟡 Média | PgBouncer configurado | Laravel configurado | ⬜ |
| 14.15 | Testar connection pooling | 1h | 🟡 Média | Configuração feita | Pooling funcionando | ⬜ |

**Total Semana 14:** 15 tarefas | ~28h | 🔴 Alta: 7 | 🟡 Média: 8

---

## 📅 Semana 15: Testes e Documentação

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 15.1 | Criar suite completa de testes E2E | 6h | 🔴 Alta | Aplicação completa | Suite criada | ⬜ |
| 15.2 | Testar fluxo completo de usuário | 3h | 🔴 Alta | Suite criada | Testes passando | ⬜ |
| 15.3 | Testar cenários de erro | 2h | 🔴 Alta | Suite criada | Testes passando | ⬜ |
| 15.4 | Configurar CI para testes E2E | 2h | 🟡 Média | Testes criados | CI configurado | ⬜ |
| 15.5 | Instalar L5-Swagger | 30 min | 🔴 Alta | Projeto Laravel | L5-Swagger instalado | ⬜ |
| 15.6 | Documentar todos os endpoints | 8h | 🔴 Alta | L5-Swagger instalado | Documentação completa | ⬜ |
| 15.7 | Adicionar exemplos de requests/responses | 4h | 🟡 Média | Documentação criada | Exemplos adicionados | ⬜ |
| 15.8 | Criar changelog da API | 1h | 🟡 Média | Documentação criada | Changelog criado | ⬜ |
| 15.9 | Criar script de rollback | 2h | 🔴 Alta | Deploy configurado | Script criado | ⬜ |
| 15.10 | Adicionar rollback ao workflow de deploy | 1h | 🔴 Alta | Script criado | Rollback no workflow | ⬜ |
| 15.11 | Testar rollback | 1h | 🔴 Alta | Rollback configurado | Teste passando | ⬜ |

**Total Semana 15:** 11 tarefas | ~30h | 🔴 Alta: 8 | 🟡 Média: 3

---

## 📅 Semana 16: Deploy e Monitoramento

| # | Tarefa | Estimativa | Prioridade | Dependências | Entregável | Status |
|---|--------|------------|------------|--------------|------------|--------|
| 16.1 | Criar docker-compose.blue-green.yml | 2h | 🟡 Média | Docker configurado | Compose criado | ⬜ |
| 16.2 | Configurar alternância entre blue/green | 2h | 🟡 Média | Compose criado | Alternância configurada | ⬜ |
| 16.3 | Testar blue-green deployment | 2h | 🟡 Média | Configuração feita | Teste passando | ⬜ |
| 16.4 | Expandir HealthController | 2h | 🔴 Alta | HealthController básico | Controller expandido | ⬜ |
| 16.5 | Adicionar check de storage | 1h | 🟡 Média | Controller expandido | Check adicionado | ⬜ |
| 16.6 | Adicionar check de cache | 1h | 🟡 Média | Controller expandido | Check adicionado | ⬜ |
| 16.7 | Criar endpoint /health/detailed | 1h | 🟡 Média | Checks adicionados | Endpoint criado | ⬜ |
| 16.8 | Configurar servidor de produção | 4h | 🔴 Alta | Ambiente de staging | Servidor configurado | ⬜ |
| 16.9 | Configurar variáveis de ambiente | 1h | 🔴 Alta | Servidor configurado | Variáveis configuradas | ⬜ |
| 16.10 | Executar migrations em produção | 1h | 🔴 Alta | Servidor configurado | Migrations executadas | ⬜ |
| 16.11 | Fazer deploy inicial | 2h | 🔴 Alta | Tudo configurado | Aplicação em produção | ⬜ |
| 16.12 | Configurar alertas no Sentry | 1h | 🔴 Alta | Sentry configurado | Alertas configurados | ⬜ |
| 16.13 | Configurar alertas de métricas | 2h | 🔴 Alta | Métricas funcionando | Alertas configurados | ⬜ |
| 16.14 | Configurar uptime monitoring | 1h | 🟡 Média | Health checks funcionando | Monitoring configurado | ⬜ |
| 16.15 | Criar dashboard de produção | 3h | 🟡 Média | Métricas funcionando | Dashboard criado | ⬜ |
| 16.16 | Testar restore de backup | 2h | 🔴 Alta | Backup configurado | Restore testado | ⬜ |
| 16.17 | Documentar procedimento de DR | 2h | 🔴 Alta | Restore testado | Documentação criada | ⬜ |
| 16.18 | Treinar equipe no procedimento | 2h | 🟡 Média | Documentação criada | Equipe treinada | ⬜ |

**Total Semana 16:** 18 tarefas | ~30h | 🔴 Alta: 9 | 🟡 Média: 9

---

## 📊 Resumo Executivo

### Distribuição de Tarefas por Prioridade

| Prioridade | Quantidade | % do Total | Total de Horas |
|------------|------------|------------|----------------|
| 🔴 **Alta** | 162 | 64% | ~828h |
| 🟡 **Média** | 72 | 28% | ~360h |
| 🟢 **Baixa** | 20 | 8% | ~100h |
| **TOTAL** | **254** | **100%** | **~1.288h** |

### Estimativas de Tempo

| Cenário | Horas/Semana | Semanas Necessárias | Meses |
|---------|--------------|---------------------|-------|
| **1 Desenvolvedor** | 40h | 32 semanas | 8 meses |
| **2 Desenvolvedores** | 80h | 16 semanas | 4 meses |
| **3 Desenvolvedores** | 120h | 10,5 semanas | ~2,6 meses |
| **4 Desenvolvedores** | 160h | 8 semanas | 2 meses |

### Dependências Críticas

1. ✅ **Fase 1** deve ser completada antes de qualquer outra fase
2. ✅ **Fase 2** (Identity/Account) é pré-requisito para Fase 3
3. ✅ **Fase 3** (Transaction) é pré-requisito para Fase 4
4. ✅ **Fases 1-4** devem estar completas antes da Fase 5 (Frontend)
5. ⚡ **Fase 6** (Segurança) pode ser paralela à Fase 5
6. ✅ **Fase 7** (Performance) depende das fases anteriores
7. ✅ **Fase 8** (Deploy) só pode iniciar após todas as outras

### Riscos Identificados

| Risco | Nível | Mitigação |
|-------|-------|-----------|
| Complexidade do DDD pode aumentar tempo | 🔴 Alto | Revisões semanais, pair programming |
| Integração frontend-backend | 🟡 Médio | Testes de integração contínuos |
| Performance não atender targets | 🟡 Médio | Otimizações desde o início |
| Dependências externas (Sentry, etc.) | 🟢 Baixo | Planos de contingência |

### Legenda

- ⬜ = Pendente
- 🔄 = Em Progresso
- ✅ = Concluído
- ❌ = Bloqueado
- 🔴 = Prioridade Alta
- 🟡 = Prioridade Média
- 🟢 = Prioridade Baixa

---

## 🔄 Controle de Progresso

### Atualização Semanal

**Última atualização:** Janeiro/2026  
**Versão:** 2.2  
**Próxima revisão:** Após conclusão de cada semana

### Changelog

**v2.2 (Janeiro/2026):**
- ✅ Adicionadas tarefas para setup do shadcn-vue (Semana 9)
- ✅ Adicionadas tarefas para Tailwind CSS e componentes UI
- ✅ Atualizado total de tarefas: 251 → 254
- ✅ Atualizado total de horas: ~1.280h → ~1.288h

**v2.1 (Janeiro/2026):**
- ✅ Adicionadas tarefas para tipo de conta "Empréstimo" (Semanas 3-4)
- ✅ Adicionadas tarefas para importação OFX (Semana 6)
- ✅ Atualizado total de tarefas: 233 → 251
- ✅ Atualizado total de horas: ~1.200h → ~1.280h

**v2.0 (Janeiro/2026):**
- ✅ Reorganização em formato tabular por semana
- ✅ Adicionadas métricas e resumos executivos

### Como Usar Este Documento

1. **Marcar tarefas concluídas:** Alterar `⬜` para `✅` na coluna Status
2. **Atualizar progresso:** Revisar métricas ao final de cada semana
3. **Ajustar estimativas:** Se necessário, atualizar estimativas baseado em aprendizado
4. **Identificar bloqueios:** Marcar tarefas bloqueadas com `❌` e documentar motivo

---

*Documento gerado em: Janeiro/2026*  
*Versão: 2.0 - Formato Tabular*
