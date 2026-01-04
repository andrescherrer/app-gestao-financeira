# Fluxo de Desenvolvimento e Padrões do Projeto

Este documento explica a finalidade de cada arquivo de configuração e como será o fluxo de desenvolvimento do projeto.

---

## 📁 Arquivos de Configuração do Cursor

Os arquivos na pasta `.cursor/rules/` são **regras do Cursor AI** que orientam o assistente durante o desenvolvimento. Eles definem padrões, convenções e fluxos de trabalho.

### 1. `.cursor/rules/adr-standards.mdc`

**Finalidade:** Define o padrão para **Architecture Decision Records (ADRs)**

**O que são ADRs?**
ADRs são documentos que registram decisões arquiteturais importantes do projeto, explicando **o que** foi decidido, **por quê** e **quais alternativas** foram consideradas.

**Quando criar um ADR:**
- ✅ Escolha de framework, biblioteca ou ferramenta
- ✅ Padrões arquiteturais (estrutura de pastas, camadas)
- ✅ Estratégias de integração com serviços externos
- ✅ Mudanças significativas em decisões anteriores
- ❌ Não criar para escolhas triviais ou bugs

**Estrutura:**
```
docs/adr/
├── 0001-usar-nuxt3-como-framework.md
├── 0002-shadcn-vue-para-componentes-ui.md
└── 0003-laravel-como-backend.md
```

**Status possíveis:**
- `Proposed` - Em discussão
- `Accepted` - Aprovado e em vigor
- `Deprecated` - Não mais recomendado
- `Superseded` - Substituído por outro ADR

**Exemplo de uso:**
Quando decidimos usar Nuxt 3 ao invés de React, criamos um ADR explicando:
- Por que escolhemos Nuxt 3
- Quais alternativas consideramos (React, SvelteKit)
- Quais são as consequências dessa decisão

---

### 2. `.cursor/rules/changelog-standards.mdc`

**Finalidade:** Define o padrão para manutenção do **CHANGELOG.md**

**O que é o CHANGELOG?**
Arquivo que documenta todas as mudanças notáveis do projeto, seguindo o formato [Keep a Changelog](https://keepachangelog.com/pt-BR/1.1.0/).

**Estrutura:**
```markdown
## [Unreleased]
### Added
- Nova funcionalidade X

## [1.0.0] - 2024-01-15
### Added
- Feature Y
### Fixed
- Bug Z
```

**Categorias:**
- `Added` - Novas funcionalidades
- `Changed` - Alterações em funcionalidades existentes
- `Deprecated` - Funcionalidades marcadas para remoção
- `Removed` - Funcionalidades removidas
- `Fixed` - Correções de bugs
- `Security` - Correções de vulnerabilidades

**Quando atualizar:**
- ✅ Nova feature implementada
- ✅ Bug corrigido
- ✅ Mudança que afeta usuários
- ❌ Não atualizar para refatorações internas sem impacto

**Exemplo:**
```markdown
### Added
- Autenticação via Google OAuth (#45)
- Endpoint GET /api/transactions/quick

### Fixed
- Corrigido cálculo de saldo global que não considerava transações pendentes (#78)
```

---

### 3. `.cursor/rules/development-workflow.mdc`

**Finalidade:** Define o **fluxo padrão de desenvolvimento** que o Cursor deve seguir

**Fluxo em 4 etapas:**

#### 1. Implementação
- Implementar seguindo padrões do projeto
- Funções/métodos pequenos e com responsabilidade única
- Nomenclatura descritiva

#### 2. Testes
Seguir a pirâmide de testes:
- **Unitários (obrigatório):** toda função com lógica de negócio
- **Integração (quando aplicável):** fluxos entre múltiplos módulos
- **E2E (features críticas):** happy path de funcionalidades core

Cobertura mínima: **80%** para código de negócio.

#### 3. Documentação
Atualizar conforme o tipo de mudança:
- **Decisão arquitetural** → Criar ADR
- **Nova feature/fix** → Adicionar ao CHANGELOG
- **API pública** → Atualizar documentação da API
- **Configuração** → Atualizar README

#### 4. Preparação do Commit
Usar **Conventional Commits**, mas **não executar automaticamente**.

**Formato:**
```
<type>(<scope>): <description>

[body opcional]

[footer opcional]
```

**Types permitidos:**
- `feat`: nova funcionalidade
- `fix`: correção de bug
- `refactor`: refatoração
- `test`: testes
- `docs`: documentação
- `style`: formatação
- `chore`: manutenção
- `perf`: performance

**Checklist antes do commit:**
- [ ] Código sem erros de lint
- [ ] Testes criados e passando
- [ ] Documentação atualizada
- [ ] Mensagem de commit no formato correto

---

### 4. `.cursor/rules/project-context.mdc`

**Finalidade:** Define o **contexto específico do projeto** (stack, estrutura, scopes, comandos)

**Informações contidas:**

#### Stack Tecnológica
- Framework: Nuxt 3
- UI: Vue 3 + shadcn-vue + Tailwind CSS
- Linguagem: TypeScript (strict mode)
- Testes: Vitest + Vue Test Utils + Playwright
- Package Manager: pnpm
- Node: >= 20.x

#### Estrutura do Projeto
```
├── .cursor/rules/         # Regras do Cursor
├── docs/
│   ├── adr/               # Architecture Decision Records
│   └── api/               # Documentação de API
├── src/
│   ├── components/        # Componentes Vue
│   │   └── ui/            # Componentes shadcn-vue
│   ├── composables/       # Composables Vue
│   ├── layouts/           # Layouts Nuxt
│   ├── pages/             # Páginas (file-based routing)
│   ├── server/            # API routes Nuxt
│   ├── stores/            # Pinia stores
│   ├── types/             # TypeScript types
│   └── utils/             # Utilitários
├── tests/
│   ├── unit/
│   ├── integration/
│   └── e2e/
```

#### Scopes para Commits
- `auth` - Autenticação
- `ui` - Componentes de interface
- `api` - Endpoints
- `transaction` - Transações
- `account` - Contas
- `goal` - Metas
- etc.

#### Comandos Disponíveis
```bash
pnpm dev              # Desenvolvimento
pnpm build            # Build
pnpm test             # Testes
pnpm lint             # Lint
```

#### Convenções de Código
- Componentes: PascalCase (`UserProfile.vue`)
- Composables: camelCase com `use` (`useAuth.ts`)
- Types: PascalCase (`User`, `TransactionResponse`)

---

### 5. `.cursor/rules/testing-standards.mdc`

**Finalidade:** Define os **padrões de teste** do projeto

**Pirâmide de Testes:**
```
        /\
       /  \        E2E (poucos)
      /----\       Fluxos críticos
     /      \
    /--------\     Integração (moderado)
   /          \    Entre módulos
  /------------\
 /              \  Unitários (muitos)
/________________\ Funções isoladas
```

**Cobertura Esperada:**
- Lógica de negócio: **80%**
- Utilitários: **90%**
- Componentes UI: **70%**
- Integrações externas: Mocks + testes de contrato

**Nomenclatura:**
```typescript
describe('[NomeDaUnidade]', () => {
  it('should [resultado esperado] when [condição]', () => {
    // Arrange
    // Act
    // Assert
  });
});
```

**Padrão AAA (Arrange-Act-Assert):**
```typescript
it('should calculate total with discount when coupon is valid', () => {
  // Arrange - preparação
  const cart = createCart([...]);
  
  // Act - execução
  const total = calculateTotal(cart, coupon);
  
  // Assert - verificação
  expect(total).toBe(225);
});
```

---

## 🔄 Fluxo Completo de Desenvolvimento

### Passo a Passo

#### 1. **Início de uma Tarefa**

Você escolhe uma tarefa do `planejamento/tarefas.md` e começa a implementar.

**Exemplo:** "Criar componente de entrada rápida de transação"

#### 2. **Implementação**

O Cursor AI usa as regras em `.cursor/rules/` para:
- Seguir a estrutura de pastas definida em `project-context.mdc`
- Aplicar convenções de nomenclatura
- Usar os padrões de código do projeto

**Código criado:**
```vue
<!-- src/components/TransactionQuickForm.vue -->
<script setup lang="ts">
// Implementação seguindo padrões
</script>
```

#### 3. **Testes**

O Cursor cria testes seguindo `testing-standards.mdc`:

```typescript
// src/components/TransactionQuickForm.test.ts
describe('TransactionQuickForm', () => {
  it('should emit submit event when form is valid', () => {
    // Arrange, Act, Assert
  });
});
```

#### 4. **Documentação**

Dependendo do tipo de mudança:

**Se for decisão arquitetural:**
- Cria ADR em `docs/adr/000X-decisao.md`
- Segue template de `adr-standards.mdc`

**Se for nova feature:**
- Adiciona entrada no `CHANGELOG.md`
- Segue formato de `changelog-standards.mdc`

**Exemplo de CHANGELOG:**
```markdown
### Added
- Componente de entrada rápida de transações (#123)
- Validação de formulário com shadcn-vue
```

#### 5. **Preparação do Commit**

O Cursor prepara o commit seguindo `development-workflow.mdc`:

**Mensagem sugerida:**
```
feat(transaction): adiciona componente de entrada rápida

- Implementa formulário com shadcn-vue
- Adiciona validação de campos
- Integra com API de transações

Closes #123
```

**Arquivos a serem commitados:**
- `src/components/TransactionQuickForm.vue`
- `src/components/TransactionQuickForm.test.ts`
- `CHANGELOG.md` (atualizado)

**O Cursor NÃO executa o commit automaticamente** - você revisa e confirma.

#### 6. **Checklist Final**

Antes de confirmar o commit, verifique:
- [ ] Código sem erros de lint
- [ ] Testes passando
- [ ] Documentação atualizada
- [ ] Mensagem de commit correta

---

## 📊 Como os Arquivos Trabalham Juntos

```
┌─────────────────────────────────────────┐
│  Você: Escolhe tarefa do planejamento  │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Cursor AI lê .cursor/rules/           │
│  - project-context.mdc (stack, estrutura)│
│  - development-workflow.mdc (fluxo)    │
│  - testing-standards.mdc (testes)      │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Implementação seguindo padrões          │
│  - Código estruturado                   │
│  - Testes criados                       │
│  - Documentação atualizada              │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Commit preparado (não executado)      │
│  - Mensagem no formato Conventional     │
│  - Arquivos listados                   │
│  - Aguardando sua confirmação          │
└─────────────────────────────────────────┘
```

---

## 🎯 Exemplo Prático Completo

### Cenário: Implementar endpoint de criação de transação

#### 1. **Implementação**
```php
// app/Interfaces/Http/Controllers/Api/V1/TransactionController.php
public function store(CreateTransactionRequest $request): JsonResponse
{
    // Implementação
}
```

#### 2. **Testes**
```php
// tests/Feature/TransactionApiTest.php
it('should create transaction when data is valid', function () {
    // Teste
});
```

#### 3. **Documentação**

**CHANGELOG.md:**
```markdown
### Added
- Endpoint POST /api/v1/transactions para criação de transações (#45)
```

**Se necessário, ADR:**
```markdown
# ADR-0004: Usar Command Pattern para transações

## Status
Accepted

## Contexto
Precisamos de uma forma consistente de criar transações...

## Decisão
Usaremos Command Pattern porque...
```

#### 4. **Commit**
```
feat(transaction): adiciona endpoint de criação

- Implementa POST /api/v1/transactions
- Adiciona validação de request
- Cria testes de feature

Closes #45
```

---

## 🔍 Resumo dos Arquivos

| Arquivo | Finalidade | Quando Usar |
|---------|------------|-------------|
| **adr-standards.mdc** | Padrão para ADRs | Ao documentar decisões arquiteturais |
| **changelog-standards.mdc** | Padrão para CHANGELOG | Ao adicionar features/fixes |
| **development-workflow.mdc** | Fluxo de desenvolvimento | Sempre (guia o Cursor) |
| **project-context.mdc** | Contexto do projeto | Sempre (define stack e estrutura) |
| **testing-standards.mdc** | Padrões de teste | Ao criar testes |
| **CHANGELOG.md** | Histórico de mudanças | Atualizar a cada feature/fix |

---

## 💡 Benefícios Deste Fluxo

1. **Consistência:** Todos seguem os mesmos padrões
2. **Qualidade:** Testes e documentação são obrigatórios
3. **Rastreabilidade:** ADRs explicam decisões importantes
4. **Histórico:** CHANGELOG documenta evolução do projeto
5. **Automação:** Cursor AI segue as regras automaticamente

---

**Última atualização:** Janeiro/2026  
**Versão:** 1.0

