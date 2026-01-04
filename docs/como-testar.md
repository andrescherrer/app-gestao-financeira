# Guia de Testes - Sistema de Gestão Financeira

Este documento descreve como testar os componentes já implementados nas Semanas 1 e 2.

---

## 📋 Índice

1. [Ambiente Docker](#ambiente-docker)
2. [Ferramentas de Qualidade](#ferramentas-de-qualidade)
3. [Feature Flags](#feature-flags)
4. [Logging](#logging)
5. [Health Checks](#health-checks)
6. [Sistema de Backup](#sistema-de-backup)
7. [CI/CD Pipeline](#cicd-pipeline)

---

## 🐳 Ambiente Docker

### Testar Configuração do Docker

```bash
# Validar sintaxe do docker-compose.yml
cd /home/andre/projetos/app-gestao-financeira
python3 -c "import yaml; yaml.safe_load(open('docker-compose.yml'))" && echo "✅ YAML válido"

# Executar script de teste
./scripts/test-docker.sh
```

### Iniciar Ambiente Completo

```bash
# Iniciar todos os serviços
docker-compose up -d

# Verificar status dos containers
docker-compose ps

# Ver logs de um serviço específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
docker-compose logs -f redis
```

### Testar Conectividade

```bash
# Testar PostgreSQL
docker-compose exec db psql -U finance -d finance_db -c "SELECT version();"

# Testar Redis
docker-compose exec redis redis-cli ping

# Testar PHP-FPM
docker-compose exec app php -v

# Testar Nginx
curl http://localhost:8080
```

### Parar Ambiente

```bash
# Parar todos os serviços
docker-compose down

# Parar e remover volumes (cuidado: apaga dados!)
docker-compose down -v
```

---

## 🔧 Ferramentas de Qualidade

### PHPStan (Análise Estática)

```bash
cd backend

# Executar análise estática
./vendor/bin/phpstan analyse

# Com mais memória (para projetos grandes)
./vendor/bin/phpstan analyse --memory-limit=2G
```

**Resultado esperado:** Análise completa sem erros críticos (pode haver warnings iniciais).

### Laravel Pint (Formatação)

```bash
cd backend

# Verificar formatação (dry-run)
./vendor/bin/pint --test

# Formatar código automaticamente
./vendor/bin/pint

# Formatar arquivo específico
./vendor/bin/pint app/Infrastructure/Services/StructuredLogger.php
```

**Resultado esperado:** Código formatado conforme padrão Laravel.

### Pest (Testes)

```bash
cd backend

# Executar todos os testes
./vendor/bin/pest

# Executar testes com cobertura
php artisan test --coverage

# Executar teste específico
./vendor/bin/pest --filter="example unit test"

# Modo watch (desenvolvimento)
./vendor/bin/pest --watch
```

**Resultado esperado:** Teste de exemplo passando.

---

## 🚩 Feature Flags

### Testar Feature Flags via Tinker

```bash
cd backend
php artisan tinker

# No Tinker:
use Laravel\Pennant\Feature;

// Verificar se feature está ativa (padrão: false)
Feature::active('new-dashboard');

// Ativar feature para usuário específico
$user = App\Models\User::first(); // ou criar um usuário de teste
Feature::for($user)->activate('new-dashboard');
Feature::for($user)->active('new-dashboard'); // deve retornar true

// Verificar todas as features
Feature::all();
```

### Testar via API (quando implementado)

```bash
# Verificar feature (exemplo futuro)
curl -X GET http://localhost:8080/api/v1/features/new-dashboard \
  -H "Authorization: Bearer {token}"
```

### Verificar Migrations do Pennant

```bash
cd backend

# Verificar se migration existe
ls -la database/migrations/*pennant*

# Executar migrations (se banco estiver configurado)
php artisan migrate
```

---

## 📝 Logging

### Testar StructuredLogger

```bash
cd backend
php artisan tinker

# No Tinker:
use App\Infrastructure\Services\StructuredLogger;

$logger = new StructuredLogger();

// Testar log de transação
$logger->logTransactionCreated('test-123', 'user-456', ['amount' => 10000]);

// Testar log de segurança
$logger->logSecurityEvent('login.attempt', 'user-456', ['success' => true]);

// Testar log de performance
$logger->logPerformance('calculate_balance', 0.125, ['accounts' => 5]);

// Testar log de erro
try {
    throw new \Exception('Test error');
} catch (\Exception $e) {
    $logger->logError('Erro de teste', $e, ['context' => 'test']);
}
```

### Verificar Logs

```bash
# Ver logs de aplicação
tail -f backend/storage/logs/application-*.log

# Ver logs de segurança
tail -f backend/storage/logs/security-*.log

# Ver logs de performance
tail -f backend/storage/logs/performance-*.log

# Ver todos os logs
ls -lah backend/storage/logs/
```

### Testar Rotação de Logs

```bash
# Os logs são rotacionados automaticamente pelo Laravel
# Verificar se logs antigos são mantidos conforme configuração:
# - application: 14 dias
# - security: 90 dias
# - performance: 7 dias

ls -lah backend/storage/logs/ | grep -E "(application|security|performance)"
```

---

## 🏥 Health Checks

### Testar Endpoint de Health

```bash
# Com ambiente Docker rodando
curl http://localhost:8080/api/health

# Resposta esperada (healthy):
# {
#   "status": "healthy",
#   "checks": {
#     "database": true,
#     "redis": true,
#     "queue": true
#   },
#   "timestamp": "2026-01-04T..."
# }

# Com formato JSON legível
curl http://localhost:8080/api/health | jq

# Verificar status HTTP
curl -i http://localhost:8080/api/health
# Deve retornar 200 OK quando healthy
```

### Testar Health Checks Individualmente

```bash
cd backend
php artisan tinker

# No Tinker:
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

// Testar database
try {
    DB::connection()->getPdo();
    echo "✅ Database OK\n";
} catch (\Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}

// Testar Redis
try {
    Redis::ping();
    echo "✅ Redis OK\n";
} catch (\Exception $e) {
    echo "❌ Redis Error: " . $e->getMessage() . "\n";
}
```

### Simular Falhas (Teste de Resiliência)

```bash
# Parar Redis temporariamente
docker-compose stop redis

# Testar health check (deve retornar unhealthy)
curl http://localhost:8080/api/health
# Deve retornar 503 com redis: false

# Reiniciar Redis
docker-compose start redis
```

---

## 💾 Sistema de Backup

### Executar Backup Manual

```bash
# Executar serviço de backup
docker-compose run --rm backup

# Verificar backup criado
ls -lah backups/

# Verificar conteúdo do backup
zcat backups/backup_*.sql.gz | head -20
```

### Testar Retenção de Backups

```bash
# Criar backup de teste
docker-compose run --rm backup

# Simular backup antigo (modificar data)
touch -t 202501010000 backups/backup_old.sql.gz

# Executar backup novamente (deve remover o antigo se > 30 dias)
docker-compose run --rm backup

# Verificar se backup antigo foi removido
ls -lah backups/
```

### Restaurar Backup (Teste)

```bash
# Listar backups disponíveis
ls -lah backups/

# Restaurar backup (exemplo)
docker-compose exec db psql -U finance -d finance_db < <(zcat backups/backup_YYYYMMDD_HHMMSS.sql.gz)
```

---

## 🔄 CI/CD Pipeline

### Testar Localmente (usando act ou GitHub Actions)

```bash
# Instalar act (opcional - para testar GitHub Actions localmente)
# https://github.com/nektos/act

# Executar workflow localmente
act push

# Ou testar jobs individualmente
act -j tests
act -j lint
```

### Verificar Sintaxe do Workflow

```bash
# Validar YAML
python3 -c "import yaml; yaml.safe_load(open('.github/workflows/ci.yml'))" && echo "✅ YAML válido"

# Verificar estrutura
cat .github/workflows/ci.yml | grep -E "(name:|on:|jobs:|steps:)"
```

### Testar Manualmente os Passos do CI

```bash
cd backend

# 1. Instalar dependências
composer install --prefer-dist --no-progress

# 2. Gerar key
php artisan key:generate

# 3. Executar migrations (requer banco configurado)
php artisan migrate --force

# 4. Executar PHPStan
./vendor/bin/phpstan analyse --memory-limit=2G

# 5. Executar Pint
./vendor/bin/pint --test

# 6. Executar testes
php artisan test --coverage --min=80
```

---

## 🧪 Testes Automatizados

### Executar Suite Completa de Testes

```bash
cd backend

# Todos os testes
php artisan test

# Com cobertura
php artisan test --coverage

# Apenas testes unitários
php artisan test --testsuite=Unit

# Apenas testes de feature
php artisan test --testsuite=Feature

# Com paralelização (se configurado)
php artisan test --parallel
```

### Criar Teste para HealthController

```bash
cd backend

# Criar teste
php artisan make:test HealthControllerTest

# Ou criar com Pest
php artisan make:test HealthControllerTest --pest
```

Exemplo de teste:

```php
// tests/Feature/HealthControllerTest.php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

test('health endpoint returns healthy status', function () {
    $response = $this->getJson('/api/health');
    
    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'checks' => [
                'database',
                'redis',
                'queue',
            ],
            'timestamp',
        ]);
});
```

---

## 📊 Checklist de Validação

### ✅ Semana 1 - Infraestrutura

- [ ] Docker Compose valida sem erros
- [ ] Todos os containers iniciam corretamente
- [ ] PostgreSQL responde a queries
- [ ] Redis responde a ping
- [ ] Nginx serve requisições na porta 8080
- [ ] Estrutura de pastas DDD está completa (66 diretórios)

### ✅ Semana 2 - Ferramentas e Configurações

- [ ] PHPStan executa sem erros críticos
- [ ] Pint formata código corretamente
- [ ] Pest executa testes com sucesso
- [ ] CI workflow tem sintaxe válida
- [ ] Feature Flags podem ser ativados/desativados
- [ ] StructuredLogger escreve nos logs corretos
- [ ] Health endpoint retorna status correto
- [ ] Script de backup cria arquivos .sql.gz
- [ ] Retenção de backups funciona (30 dias)

---

## 🐛 Troubleshooting

### Problemas Comuns

#### Docker não inicia
```bash
# Verificar se Docker está rodando
docker info

# Verificar logs de erro
docker-compose logs

# Limpar e recriar
docker-compose down -v
docker-compose up -d --build
```

#### PHPStan encontra muitos erros
```bash
# Ajustar nível de strictness no phpstan.neon
# level: 5 (atual) pode ser reduzido para 3 ou 4 inicialmente
```

#### Health check retorna unhealthy
```bash
# Verificar se serviços estão rodando
docker-compose ps

# Verificar conectividade
docker-compose exec app php artisan tinker
# Testar DB::connection() e Redis::ping()
```

#### Logs não aparecem
```bash
# Verificar permissões
chmod -R 775 backend/storage/logs

# Verificar configuração
cat backend/config/logging.php | grep -A 5 "application"
```

---

## 📚 Próximos Passos

Após validar todos os componentes:

1. **Semana 3**: Implementar Value Objects e Entidades
2. **Semana 4**: Criar endpoints de autenticação e contas
3. **Semana 5**: Implementar transações e categorias

---

*Última atualização: Janeiro/2026*

