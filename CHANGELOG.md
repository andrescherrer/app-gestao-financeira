# Changelog

Todas as mudanças notáveis deste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

### Added

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

<!-- 
## [0.1.0] - YYYY-MM-DD

### Added
- Feature X

### Fixed
- Bug Y

[Unreleased]: https://github.com/usuario/projeto/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/usuario/projeto/releases/tag/v0.1.0
-->
