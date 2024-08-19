<?php
declare(strict_types=1);
namespace Entity;

require_once 'src/Class/Address.php';
require_once 'src/Class/City.php';

use PDO;

class Database
{
    private const DB_HOST = 'localhost';
    private const DB_PORT = '3306';
    private const DB_NAME = 'app';
    private const DB_USERNAME = 'root';
    private const DB_PASSWORD = '';

    private PDO $connection;

    /**
     * Used to get className without namespace
     * @param $class object|string
     * @return string
     */
    public function getShortClassName(object|string $class): string
    {
        $parts = explode('\\', is_object($class) ? $class::class : $class);
        return strtolower(end($parts));
    }

    public function connect(): void
    {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s', self::DB_HOST, self::DB_PORT, self::DB_NAME);

        try {
            $this->connection = new PDO($dsn, self::DB_USERNAME, self::DB_PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function insert(object $entity): bool
    {
        $tableName = strtolower($this->getShortClassName($entity));
        $reflection = new \ReflectionClass($entity);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $properties = [];
        foreach ($methods as $method) {
            if (strpos($method->getName(),'get') === 0) {
                $propertyName = lcfirst(substr($method->getName(), 3));
                $properties[$propertyName] = $entity->{$method->getName()}();
            }
        }

        // Get the correct columns
        // Placeholders are used later for bindValue
        $columns = implode(', ', array_keys($properties));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($properties)));

        $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);

        foreach ($properties as $key => $value) {
            if(is_object($value) && method_exists($value, 'format')) {
                $stmt->bindValue(":$key", $value->format('Y-m-d H:i:s'));
            } else {
                $stmt->bindValue(":$key", is_object($value) ? $value->getId() : $value);
            }
        }

        return $stmt->execute();
    }

    public function update(object $entity): bool
    {
        $tableName = strtolower($this->getShortClassName($entity));
        $reflection = new \ReflectionClass($entity);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $properties = [];
        foreach ($methods as $method) {
            if (strpos($method->getName(),'get') === 0) {
                $propertyName = lcfirst(substr($method->getName(), 3));
                $properties[$propertyName] = $entity->{$method->getName()}();
            }
        }
        $id = $properties['id'];
        unset($properties['id']);

        // Get the correct setClause
        // This is used later for bindValue
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($properties)));

        $sql = "UPDATE $tableName SET $setClause WHERE id = :id";
        $stmt = $this->connection->prepare($sql);

        foreach ($properties as $key => $value) {
            if(is_object($value) && method_exists($value, 'format')) {
                $stmt->bindValue(":$key", $value->format('Y-m-d H:i:s'));
            } else {
                $stmt->bindValue(":$key", is_object($value) ? $value->getId() : $value);
            }
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function get(string $className, int $id): ?object
    {
        $tableName = $this->getShortClassName($className);

        $sql = "SELECT * FROM $tableName WHERE id = :id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $entity = new $className();
            foreach ($result as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($entity, $method)) {
                    $paramType = $this->getParameterType($entity, $method);
                    if(\class_exists($paramType)) {
                        $entity->$method($this->get($paramType, $value));
                    } else {
                        $entity->$method($value);
                    }
                }
            }
            return $entity;
        }

        return null;
    }

    public function readAll(string $className): array
    {
        try {
            $tableName = $this->getShortClassName($className);

            $sql = "SELECT * FROM $tableName";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $entities = [];

            foreach ($results as $result) {
                $entity = new $className();
                foreach ($result as $key => $value) {
                    $method = 'set' . ucfirst($key);
                    if (method_exists($entity, $method)) {
                        $paramType = $this->getParameterType($entity, $method);
                        if(\class_exists($paramType)) {
                            $entity->$method($this->get($paramType, $value));
                        } else {
                            $entity->$method($value);
                        }
                    }
                }
                $entities[] = $entity;
            }
        } catch (\PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }

        return $entities;
    }

    public function delete(string $className, int $id): bool
    {
        $tableName = $this->getShortClassName($className);

        $sql = "DELETE FROM $tableName WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    /**
     * @throws \ReflectionException
     */
    private function getParameterType(object $object, string $methodName): bool|string
    {
        $reflectionMethod = new \ReflectionMethod($object, $methodName);
        $parameters = $reflectionMethod->getParameters();

        if (isset($parameters[0])) {
            $parameter = $parameters[0];
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType) {
                return $type->getName();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}