# Changelog

Todas as mudanças notáveis deste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

### Added

- Value Objects implementados (Semana 3 - Tarefas 3.1-3.5)
  - Value Object `Money` para operações monetárias
    - Suporte a centavos e decimais
    - Operações: add, subtract, multiply
    - Comparações e formatação
    - Validação de moedas
  - Value Object `Document` para CPF/CNPJ
    - Validação completa de CPF e CNPJ
    - Formatação automática
    - Detecção automática do tipo
  - Value Object `Email` para endereços de email
    - Validação com filter_var
    - Imutável e type-safe
  - Value Object `Uuid` para identificadores únicos
    - Integração com ramsey/uuid
    - Geração e validação de UUIDs
  - Testes unitários completos para todos os Value Objects
    - 36 testes passando (66 assertions)
    - Cobertura: Money 100%, Document 96.4%, Email 100%, Uuid 100%
  - Dependência `ramsey/uuid` adicionada ao projeto
- Entidades de domínio implementadas (Semana 3 - Tarefas 3.6-3.15)
  - Entidade `User` para usuários do sistema
    - Suporte a email, senha e organização
    - Métodos para atualização de dados
  - Entidade `Organization` para organizações (PF/PJ)
    - Suporte a documento (CPF/CNPJ)
    - Soft deletes implementado
    - Planos de assinatura
  - Entidade `Account` para contas financeiras
    - Suporte a diferentes tipos de conta
    - Campos específicos para empréstimos (borrower_id, interest_rate, loan_due_date)
    - Soft deletes implementado
  - Entidade `AccountType` para tipos de conta
    - Suporte a credit_limit e borrower
  - Migrations criadas e executadas
    - `organizations` com UUID, documento, tipo e plano
    - `users` atualizada para UUID e organization_id
    - `account_types` com slug, has_credit_limit, supports_borrower
    - `accounts` com todos os campos incluindo empréstimos
  - Seeder `AccountTypeSeeder` criado e executado
    - Tipos: checking, credit_card, investment, loan
    - Dados populados no banco de dados

- Configuração inicial do projeto
- Projeto Laravel instalado via Composer (v12.44.0)
- Estrutura base do backend criada em `backend/`
- Arquivo `.env` configurado com variáveis de ambiente do projeto
  - PostgreSQL como banco de dados padrão
  - Redis para cache, sessões e filas
  - Locale configurado para pt_BR
  - APP_KEY gerada automaticamente
- Namespaces e autoload configurados para arquitetura DDD
  - `App\Domain\` para camada de domínio
  - `App\Application\` para camada de aplicação
  - `App\Infrastructure\` para camada de infraestrutura
  - `App\Interfaces\` para camada de interface
  - Estrutura de diretórios criada conforme planejamento
- Arquivo `docker-compose.yml` criado com configuração completa
  - Serviços: app (PHP-FPM), nginx, PostgreSQL, Redis
  - Queue worker e scheduler para processamento assíncrono
  - Health checks para PostgreSQL e Redis
  - Volumes persistentes para dados do banco e Redis
  - Network isolada para comunicação entre serviços
- Dockerfile para PHP-FPM criado em `docker/Dockerfile`
  - Baseado em PHP 8.3-FPM Alpine
  - Extensões: pdo_pgsql, redis, bcmath, intl, opcache, pcntl
  - Composer instalado
  - Usuário não-root configurado
- Configurações PHP criadas em `docker/php/local.ini`
  - Limites de upload e memória configurados
  - OPcache otimizado para desenvolvimento
- Configuração Nginx criada em `docker/nginx/default.conf`
  - Proxy reverso para PHP-FPM
  - Gzip habilitado
  - Segurança básica configurada
- Validação e testes do ambiente Docker
  - Nginx, PostgreSQL e Redis validados no docker-compose.yml
  - Script de teste criado (`scripts/test-docker.sh`)
  - Health checks configurados para PostgreSQL e Redis
- Estrutura completa de pastas DDD criada
  - Domain: Identity, Account, Transaction, Planning, Shared (com subpastas)
  - Application: Commands, Queries, Bus, DTOs por domínio
  - Infrastructure: Persistence, Bus, Cache, FeatureFlags, Jobs, Services
  - Interfaces: Http (Controllers, Requests, Resources, Middleware), Console
  - Total de 66 diretórios criados conforme planejamento
- Ferramentas de qualidade de código instaladas e configuradas
  - PHPStan instalado com Larastan (nível 5)
  - Arquivo `phpstan.neon` configurado com regras para Laravel
  - Laravel Pint configurado com preset Laravel
  - Arquivo `pint.json` criado
  - Pest instalado com plugin Laravel
  - Arquivo `tests/Pest.php` configurado
  - Teste de exemplo criado e funcionando
- CI/CD Pipeline configurado
  - Workflow GitHub Actions criado (`.github/workflows/ci.yml`)
  - Serviços PostgreSQL e Redis configurados no CI
  - Jobs de lint (Pint) e análise estática (PHPStan) adicionados
  - Testes automatizados com cobertura mínima de 80%
- Feature Flags configurados
  - Laravel Pennant instalado
  - Migrations do Pennant publicadas
  - FeatureFlagServiceProvider criado com features iniciais
  - Features: new-dashboard, quick-transaction-v2, ofx-import, loan-accounts
- Logging estruturado implementado
  - Canais de log configurados: application, security, performance
  - StructuredLogger criado com métodos para eventos específicos
  - Rotação de logs configurada (14 dias para application, 90 dias para security)
- Health Checks implementados
  - HealthController criado com checks de database, Redis e queue
  - Rota `/api/health` configurada
  - Retorna status 200 (healthy) ou 503 (unhealthy)
- Sistema de backup configurado
  - Serviço de backup adicionado ao docker-compose.yml
  - Script de backup criado (`scripts/backup.sh`)
  - Retenção de backups configurada (30 dias por padrão)

### Fixed

- Corrigido Dockerfile para usar PHP 8.4 (requerido pelo Laravel 12)
- Removido atributo `version` obsoleto do docker-compose.yml
- Health endpoint validado e funcionando corretamente
- Xdebug instalado e configurado para cobertura de testes
- Configuração do Xdebug otimizada (modo coverage apenas)

<!-- 
## [0.1.0] - YYYY-MM-DD

### Added
- Feature X

### Fixed
- Bug Y

[Unreleased]: https://github.com/usuario/projeto/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/usuario/projeto/releases/tag/v0.1.0
-->
