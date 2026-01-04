#!/bin/bash

# Script de backup do PostgreSQL
# Tarefa 2.24: Criar script de backup

set -e

# Configurações
BACKUP_DIR="/backups"
RETENTION_DAYS=${BACKUP_RETENTION_DAYS:-30}
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.sql.gz"

# Criar diretório de backup se não existir
mkdir -p "${BACKUP_DIR}"

# Executar backup
echo "Iniciando backup do banco de dados..."
pg_dump -h "${PGHOST}" -p "${PGPORT}" -U "${PGUSER}" -d "${PGDATABASE}" \
    | gzip > "${BACKUP_FILE}"

if [ $? -eq 0 ]; then
    echo "Backup criado com sucesso: ${BACKUP_FILE}"
    
    # Calcular tamanho do backup
    BACKUP_SIZE=$(du -h "${BACKUP_FILE}" | cut -f1)
    echo "Tamanho do backup: ${BACKUP_SIZE}"
    
    # Limpar backups antigos (retenção)
    echo "Removendo backups mais antigos que ${RETENTION_DAYS} dias..."
    find "${BACKUP_DIR}" -name "backup_*.sql.gz" -type f -mtime +${RETENTION_DAYS} -delete
    
    echo "Backup concluído com sucesso!"
    exit 0
else
    echo "Erro ao criar backup!"
    exit 1
fi

