DO
$$
BEGIN
   IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'task_manager_user') THEN
      CREATE ROLE task_manager_user LOGIN PASSWORD 'task_manager_password';
   END IF;
END
$$;

SELECT 'CREATE DATABASE task_manager OWNER task_manager_user'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'task_manager')\gexec

GRANT ALL PRIVILEGES ON DATABASE task_manager TO task_manager_user;