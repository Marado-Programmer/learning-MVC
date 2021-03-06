<?php

/**
 *
 */

class DBConnection implements DBMethods
{
    private DBService $connection;

    private static $cache;
    private $error;

    private $invalidPatterns = [
        '/OR\s+1=1/i',       // OR 1=1
        '/"\s+OR\s+""="/i',  // " OR ""="
        '/;/',              // ;
        '/--/',             // --
        '/\/\*.*\*\//'      // /* */
    ];

    public function __construct()
    {
        $this->connection = DBService::getInstance();
        self::$cache = [];
    }

    public function checkConnection()
    {
        return (bool) $this->connection;
    }

    public function beginTransaction()
    {
        $this->connection->beginTransaction();
    }

    public function commit()
    {
        $this->connection->commit();
    }

    public function rollBack()
    {
        $this->connection->rollBack();
    }

    public function query(PDOStatement|string $statement, array $dataArray = [])
    {
        if (is_string($statement))
            $statement = $this->createQuery($statement);

        // Protect against SQL Injections
        if ($this->checkForSQLInjections($dataArray));

        $key = $statement->queryString . serialize($dataArray);
        if (isset(self::$cache[$key]))
            return self::$cache[$key];

        return $this->connection->query($statement, $dataArray);
    }

    public function selectToObject(PDOStatement $statement, array $dataArray = [], string $class, array $useOnConstruct)
    {
        $data = $this->query($statement, $dataArray);

        if (!isset(self::$cache[$statement])) {
            $result = $data->fetch(PDO::FETCH_ASSOC);

            $params = [];
            foreach ($useOnConstruct as $attr)
                foreach ($result as $key => $param)
                    if ($attr == $key) {
                        $params[$attr] = $param;

                        break;
                    }

            $result = new $class(extract($params)); // extract returns int, this doesn't work

            self::$cache[$statement] = $result;
        }

        return self::$cache[$statement];
    }

    private function checkForSQLInjections(array $userInput = []) {
        foreach ($userInput as $param)
            foreach ($this->invalidPatterns as $pattern)
                if (preg_match($pattern, $param))
                    throw new Exception('Invalid input');
        return true;
    }

    public function createQuery(string $query)
    {
        $statement = $this->connection->getPDO()->prepare($query);

        if (!$statement)
            throw new Exception($statement->errorInfo()[2]);

        return $statement;
    }

    public function insert(string $table, array ...$inserts)
    {
        $cols = [];
        $placeHolders = '(';
        $values = [];

        foreach ($inserts as $arr) {
            if (!isset($arr) || !is_array($arr))
                return;
        }

        for ($i = 0; $i < count($inserts); $i++)
            foreach ($inserts[$i] as $col => $val) {
                if ($i === 0)
                    $cols[] = "`$col`";

                if ($i != 0)
                    $placeHolders .= '), (';

                $placeHolders .= '?, ';

                $values[] = $val;
            }

        $placeHolders = substr($placeHolders, 0, strlen($placeHolders) - 2);

        $cols = implode(', ', $cols);

        $stmt = "INSERT INTO $table($cols) VALUES $placeHolders)";
        $insert = $this->query($this->createQuery($stmt), $values);

        if ($insert) {
            if (
                method_exists($this->connection->getPDO(), 'lastInsertId')
                && $this->connection->getPDO()->lastInsertId()
            )
                $this->lastId = $this->connection->getPDO()->lastInsertId();
            return $insert;
        }
        return;
    }

    public function update($table, array $where_params, $values)
    {
        if (empty($table) || empty($where_params))
            return;

        $stmt = "UPDATE `$table` SET";
        $set = array();

        $where = " WHERE";
        $i = 0;
        foreach ($where_params as $key => $val) {
            if ($i++)
                $where .= " AND";
            $where .= " `$key` = ? ";
        }

        if (!is_array($values))
            return;

        foreach ($values as $column => $value)
            $set[] = "`$column` = ?";

        $set = implode(', ', $set);

        $stmt .= $set . $where;

        $values = array_merge($values, $where_params);
        $values = array_values($values);

        $update = $this->query($this->createQuery($stmt), $values);

        if ($update)
            return $update;

        return;
    }

    public function delete($table, array $where_params)
    {
        if (empty($table) || empty($where_params))
            return;

        $where = " WHERE";
        $i = 0;
        foreach ($where_params as $key => $val) {
            if ($i++)
                $where .= " AND";
            $where .= " `$key` = ? ";
        }

        $stmt = "DELETE FROM `$table`";
        $stmt .= $where;

        $delete = $this->query($this->createQuery($stmt), array_values($where_params));

        if ($delete)
            return $delete;

        return;
    }

    public function resultToCache(PDOStatement $query, array $dataArray, object $result, $force = false)
    {
        $key = $query->queryString . serialize($dataArray);

        if (!isset(self::$cache[$key]) || $force)
            self::$cache[$key] = $result;
    }

    public function checkAccess()
    {
        if (!$this->connection->getPDO())
            die('Connection Error.');
    }

    public function getErrors()
    {
        return $this->error;
    }
}

