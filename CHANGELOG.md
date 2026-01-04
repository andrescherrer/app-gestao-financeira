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

<!-- 
## [0.1.0] - YYYY-MM-DD

### Added
- Feature X

### Fixed
- Bug Y

[Unreleased]: https://github.com/usuario/projeto/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/usuario/projeto/releases/tag/v0.1.0
-->
