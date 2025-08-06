# API RESTful

## Requisitos

- PHP >= 7.4 (idealmente PHP 8+)
- Servidor web con soporte PHP (Apache, Nginx, etc.)
- Composer (para manejo de dependencias)
- Base de datos MySQL o MariaDB

## Instalación

1. Clonar el repositorio:

```bash
git clone https://github.com/FreilaN2/API-USER.git
cd API-USER
```

2. Instalar dependencias:

```
composer install
```

3. Editar conexión con la BD:

```
config/database.php:

<?php
class Database {
    private $host = 'localhost:3306'; Aquí debe colocar el puerto que usa, si es XAMPP por lo general es 3306
    private $db_name = 'api'; Aquí debe editar el nombre de la BD (en caso de que lo cambie)
    private $username = 'root'; Su usuario
    private $password = ''; Su contraseña, si no tiene, lo deja vacío
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }

        return $this->conn;
    }
}
```

4. Importar la BD:

El archivo database.sql debe importarlo en su gestor de base de datos, tenga en cuenta que este archivo solo crea la tabla de users, por lo que deberá crear antes la BD y luego importar el archivo dentro de la misma.

5. Levantar el servidor local:

```
php -S localhost:8000 -t public
```

## Prueba

Para realizar las pruebas puede utilizar curl (desde VS Code debe abrir Git bash para utilizar los comandos), a continuación se presentan los comandos necesarios para realizar las pruebas:

Registro de usuario
```
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{"nombre":"usuario","email":"usuario@example.com","password":"MiContraseña123!"}'
```

Login
```
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","password":"MiContraseña123!"}'
```

Ver perfil
```
curl -X GET http://localhost:8000/profile \
  -H "Authorization: Bearer TU_TOKEN"
```

Listar usuarios
```
curl -X GET "http://localhost:8000/users?page=1&limit=5" \
  -H "Authorization: Bearer TU_TOKEN"
```
