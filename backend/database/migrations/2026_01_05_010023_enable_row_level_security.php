<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // RLS só funciona no PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Habilitar RLS nas tabelas principais
        DB::statement('ALTER TABLE accounts ENABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE organizations ENABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE users ENABLE ROW LEVEL SECURITY;');

        // Criar função para obter organization_id do usuário atual
        DB::statement("
            CREATE OR REPLACE FUNCTION get_current_organization_id()
            RETURNS uuid AS $$
            BEGIN
                RETURN current_setting('app.current_organization_id', true)::uuid;
            EXCEPTION
                WHEN OTHERS THEN
                    RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Política RLS para accounts
        DB::statement('
            CREATE POLICY tenant_isolation_accounts ON accounts
                FOR ALL
                USING (organization_id = get_current_organization_id());
        ');

        // Política RLS para organizations
        DB::statement('
            CREATE POLICY tenant_isolation_organizations ON organizations
                FOR ALL
                USING (id = get_current_organization_id());
        ');

        // Política RLS para users
        DB::statement('
            CREATE POLICY tenant_isolation_users ON users
                FOR ALL
                USING (organization_id = get_current_organization_id());
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // RLS só funciona no PostgreSQL
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP POLICY IF EXISTS tenant_isolation_accounts ON accounts;');
        DB::statement('DROP POLICY IF EXISTS tenant_isolation_organizations ON organizations;');
        DB::statement('DROP POLICY IF EXISTS tenant_isolation_users ON users;');
        DB::statement('DROP FUNCTION IF EXISTS get_current_organization_id();');
        DB::statement('ALTER TABLE accounts DISABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE organizations DISABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE users DISABLE ROW LEVEL SECURITY;');
    }
};
