#!/bin/bash

# Script para testar o ambiente Docker completo
# Tarefa 1.9: Testar ambiente Docker completo

set -e

echo "🐳 Testando ambiente Docker completo..."
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para verificar se o Docker está rodando
check_docker() {
    if ! command -v docker &> /dev/null; then
        echo -e "${RED}❌ Docker não está instalado${NC}"
        exit 1
    fi
    
    if ! docker info &> /dev/null; then
        echo -e "${RED}❌ Docker não está rodando${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Docker está instalado e rodando${NC}"
}

# Função para verificar se o Docker Compose está disponível
check_docker_compose() {
    if command -v docker-compose &> /dev/null; then
        COMPOSE_CMD="docker-compose"
    elif docker compose version &> /dev/null; then
        COMPOSE_CMD="docker compose"
    else
        echo -e "${RED}❌ Docker Compose não está disponível${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Docker Compose está disponível${NC}"
    echo "   Usando: $COMPOSE_CMD"
}

# Função para validar arquivos de configuração
validate_config_files() {
    echo ""
    echo "📋 Validando arquivos de configuração..."
    
    local errors=0
    
    if [ ! -f "docker-compose.yml" ]; then
        echo -e "${RED}❌ docker-compose.yml não encontrado${NC}"
        errors=$((errors + 1))
    else
        echo -e "${GREEN}✅ docker-compose.yml encontrado${NC}"
    fi
    
    if [ ! -f "docker/Dockerfile" ]; then
        echo -e "${RED}❌ docker/Dockerfile não encontrado${NC}"
        errors=$((errors + 1))
    else
        echo -e "${GREEN}✅ docker/Dockerfile encontrado${NC}"
    fi
    
    if [ ! -f "docker/nginx/default.conf" ]; then
        echo -e "${RED}❌ docker/nginx/default.conf não encontrado${NC}"
        errors=$((errors + 1))
    else
        echo -e "${GREEN}✅ docker/nginx/default.conf encontrado${NC}"
    fi
    
    if [ ! -f "docker/php/local.ini" ]; then
        echo -e "${RED}❌ docker/php/local.ini não encontrado${NC}"
        errors=$((errors + 1))
    else
        echo -e "${GREEN}✅ docker/php/local.ini encontrado${NC}"
    fi
    
    if [ $errors -gt 0 ]; then
        echo -e "${RED}❌ Encontrados $errors erro(s) na validação de arquivos${NC}"
        exit 1
    fi
}

# Função para validar sintaxe do docker-compose.yml
validate_compose_syntax() {
    echo ""
    echo "🔍 Validando sintaxe do docker-compose.yml..."
    
    if $COMPOSE_CMD config --quiet > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Sintaxe do docker-compose.yml está correta${NC}"
    else
        echo -e "${RED}❌ Erro na sintaxe do docker-compose.yml${NC}"
        $COMPOSE_CMD config
        exit 1
    fi
}

# Função para verificar serviços no docker-compose.yml
check_services() {
    echo ""
    echo "🔍 Verificando serviços configurados..."
    
    local required_services=("app" "nginx" "db" "redis" "queue" "scheduler")
    local found_services=0
    
    for service in "${required_services[@]}"; do
        if grep -q "^  $service:" docker-compose.yml; then
            echo -e "${GREEN}✅ Serviço '$service' encontrado${NC}"
            found_services=$((found_services + 1))
        else
            echo -e "${RED}❌ Serviço '$service' não encontrado${NC}"
        fi
    done
    
    if [ $found_services -eq ${#required_services[@]} ]; then
        echo -e "${GREEN}✅ Todos os serviços necessários estão configurados${NC}"
    else
        echo -e "${RED}❌ Faltam serviços no docker-compose.yml${NC}"
        exit 1
    fi
}

# Função para testar build (opcional, pode ser demorado)
test_build() {
    echo ""
    read -p "🔨 Deseja testar o build das imagens? (pode demorar alguns minutos) [y/N]: " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🔨 Construindo imagens..."
        if $COMPOSE_CMD build --no-cache > /tmp/docker-build.log 2>&1; then
            echo -e "${GREEN}✅ Build das imagens concluído com sucesso${NC}"
        else
            echo -e "${RED}❌ Erro no build das imagens${NC}"
            echo "   Verifique o log em /tmp/docker-build.log"
            exit 1
        fi
    else
        echo -e "${YELLOW}⏭️  Build pulado${NC}"
    fi
}

# Função para testar inicialização dos containers (opcional)
test_start() {
    echo ""
    read -p "🚀 Deseja testar a inicialização dos containers? [y/N]: " -n 1 -r
    echo
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🚀 Iniciando containers..."
        if $COMPOSE_CMD up -d > /dev/null 2>&1; then
            echo -e "${GREEN}✅ Containers iniciados${NC}"
            
            echo ""
            echo "⏳ Aguardando serviços ficarem prontos (30 segundos)..."
            sleep 30
            
            echo ""
            echo "🔍 Verificando status dos containers..."
            $COMPOSE_CMD ps
            
            echo ""
            echo "🧹 Parando containers..."
            $COMPOSE_CMD down
            echo -e "${GREEN}✅ Containers parados${NC}"
        else
            echo -e "${RED}❌ Erro ao iniciar containers${NC}"
            exit 1
        fi
    else
        echo -e "${YELLOW}⏭️  Teste de inicialização pulado${NC}"
    fi
}

# Main
main() {
    echo "=========================================="
    echo "  Teste do Ambiente Docker Completo"
    echo "  Tarefa 1.9"
    echo "=========================================="
    echo ""
    
    check_docker
    check_docker_compose
    validate_config_files
    validate_compose_syntax
    check_services
    test_build
    test_start
    
    echo ""
    echo "=========================================="
    echo -e "${GREEN}✅ Todos os testes passaram!${NC}"
    echo "=========================================="
    echo ""
    echo "📝 Próximos passos:"
    echo "   1. Execute 'docker-compose up -d' para iniciar o ambiente"
    echo "   2. Acesse http://localhost:8080 para verificar o Nginx"
    echo "   3. Verifique os logs com 'docker-compose logs -f'"
}

main "$@"

