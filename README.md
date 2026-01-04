# Sistema de Gestão Financeira

Aplicação web completa para gestão financeira pessoal e empresarial, desenvolvida com Laravel (backend) e Nuxt 3 com Vue 3 (frontend), seguindo os princípios de Domain-Driven Design (DDD) e Clean Architecture.

## 📋 Sobre o Projeto

Sistema de gestão financeira que permite aos usuários controlar suas finanças de forma intuitiva, com suporte a múltiplos tipos de contas (corrente, cartão de crédito, investimento, empréstimo), transações, metas e planejamento financeiro.

### Principais Funcionalidades

- ✅ Gestão de transações (receitas e despesas)
- ✅ Importação automática via arquivo OFX
- ✅ Múltiplos tipos de conta (corrente, crédito, investimento, empréstimo)
- ✅ Saldo global consolidado
- ✅ Sistema de metas e objetivos
- ✅ Suporte a PF e PJ (multi-tenancy)
- ✅ Interface intuitiva e responsiva

## 📚 Documentação

### Documentação Principal

- **[Resumo dos Objetivos](docs/resumo-objetivos.md)**  
  Resumo executivo dos objetivos funcionais e técnicos do projeto, escopo, fases e métricas de sucesso.

- **[Planejamento Completo](docs/planejamento-sistema-financeiro.md)**  
  Documento completo com arquitetura, DDD, modelagem de dados, API endpoints, otimizações de performance, segurança, testes e muito mais.

- **[Verificação de Requisitos](docs/verificacao-requisitos.md)**  
  Comparação entre os requisitos esperados e o que foi planejado, identificando correspondências e lacunas.

- **[Planejamento de Tarefas](planejamento/tarefas.md)**  
  Planejamento detalhado em formato tabular com 251 tarefas organizadas por 16 semanas (~1.280 horas).

## 🏗️ Arquitetura

### Stack Tecnológica

- **Backend:** Laravel (API REST)
- **Frontend:** Nuxt 3 com Vue 3 e TypeScript
- **UI Components:** shadcn-vue (componentes acessíveis e customizáveis)
- **Banco de Dados:** PostgreSQL
- **Cache:** Redis
- **Queue:** Processamento assíncrono
- **Containerização:** Docker

### Princípios Arquiteturais

- **Domain-Driven Design (DDD)** com Bounded Contexts
- **Clean Architecture** com separação de camadas
- **CQRS** para otimização de leituras
- **Multi-tenancy** com isolamento completo de dados

## 📊 Escopo do Projeto

- **Duração:** 16 semanas (4 meses)
- **Total de Tarefas:** 251 tarefas
- **Total de Horas:** ~1.280 horas
- **Metodologia:** Desenvolvimento incremental com entregas semanais

### Fases de Desenvolvimento

1. **Setup Inicial** (Semanas 1-2) - Infraestrutura, Docker, CI/CD
2. **Domain Identity e Account** (Semanas 3-4) - Usuários, organizações, contas
3. **Domain Transaction** (Semanas 5-6) - Transações, categorias, importação OFX
4. **Domain Planning** (Semanas 7-8) - Metas e objetivos
5. **Frontend + Integração** (Semanas 9-10) - Interface e dashboard
6. **Segurança e Compliance** (Semanas 11-12) - 2FA, OAuth2, LGPD
7. **Observabilidade e Performance** (Semanas 13-14) - Monitoramento e otimizações
8. **Deploy e Infraestrutura** (Semanas 15-16) - Produção e DR

## 🚀 Início Rápido

> ⚠️ **Nota:** O projeto está em fase de planejamento. A implementação seguirá o planejamento detalhado em [planejamento/tarefas.md](planejamento/tarefas.md).

### Pré-requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- Docker e Docker Compose
- PostgreSQL 15+

### Instalação

```bash
# Clonar o repositório
git clone <repository-url>
cd app-gestao-financeira

# Instalar dependências (quando disponível)
composer install
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Subir containers Docker
docker-compose up -d

# Executar migrations (quando disponível)
php artisan migrate
```

## 📖 Estrutura de Documentação

```
docs/
├── resumo-objetivos.md              # Resumo executivo dos objetivos
├── planejamento-sistema-financeiro.md  # Planejamento completo
└── verificacao-requisitos.md        # Verificação de requisitos

planejamento/
└── tarefas.md                       # Tarefas detalhadas por semana
```

## 🎯 Objetivos do Projeto

### Funcionais
- Gestão completa de transações financeiras
- Suporte a múltiplos tipos de conta
- Sistema de metas e planejamento
- Importação automática de extratos (OFX)
- Interface intuitiva e responsiva

### Técnicos
- Arquitetura robusta e escalável
- Alta performance com cache multi-camada
- Segurança e compliance (LGPD)
- Testes abrangentes e CI/CD
- Observabilidade completa

## 🔒 Segurança

- Autenticação via Laravel Sanctum
- 2FA/MFA para segurança adicional
- Rate limiting para proteção
- Row-Level Security (RLS) para isolamento
- Logs de auditoria completos
- Compliance LGPD

## 📈 Performance

- Cache multi-camada (aplicação, query, HTTP)
- CQRS com Read Models
- Connection pooling (PgBouncer)
- Processamento assíncrono
- Otimizações de banco de dados
- Paginação por cursor

## 🧪 Qualidade

- Testes unitários, integração, feature e E2E
- Análise estática (PHPStan)
- Formatação automática (Laravel Pint)
- CI/CD automatizado
- Code reviews obrigatórios

## 📝 Licença

Este projeto está em desenvolvimento.

## 👥 Contribuindo

> ⚠️ O projeto está em fase de planejamento. Contribuições serão bem-vindas após o início da implementação.

## 📞 Contato

Para mais informações, consulte a [documentação completa](docs/planejamento-sistema-financeiro.md).

---

**Última atualização:** Janeiro/2026  
**Status:** Planejamento Completo ✅
