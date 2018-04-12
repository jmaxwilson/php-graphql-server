<?php

namespace PHPGraphQLServer\DataAccess;

use PHPGraphQLServer\DataAccess\PDOConnection;
use \PDO;
use \Exception;

class DataAccessObject
{
    /**
     * $db PDO
     */
    protected $db;

    function __construct(PDO $db = null)
	{
		if (!$db instanceof \PDO)
		{
            $db = PDOConnection::getDB();
        }
        
		$this->db = $db;
    }

    function throwPDOError($statement)
    {
        $error = sprintf('SQL Error (%s): %s', $statement->errorInfo()[2], $statement->queryString);
		throw new Exception($error);
    }
    
    function execute($sql, $params=[])
	{
        $sql_processed = $this->processArrayParams($sql, $params);
		$statement = $this->db->prepare($sql_processed['sql']);
        if (!$statement->execute($sql_processed['params'])) { $this->throwPDOError($statement); }
        
		return $statement;
    }

    function selectRow($sql, $params=[])
    {
        $sql_processed = $this->processArrayParams($sql, $params);
        $statement = $this->db->prepare($sql_processed['sql']);
		if (!$statement->execute($sql_processed['params'])) { $this->throwPDOError($statement); }

		return $statement->fetch(PDO::FETCH_ASSOC);
    }

    function selectRows($sql, $params=[])
    {
        $sql_processed = $this->processArrayParams($sql, $params);
        $statement = $this->db->prepare($sql_processed['sql']);
        if (!$statement->execute($sql_processed['params'])) { $this->throwPDOError($statement); }
        
        $rows = [];
		while ($row = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$rows[] = $row;
		}

		return $rows;
    }

    function selectValue($sql, $params = [])
    {
        $sql_processed = $this->processArrayParams($sql, $params);
        $statement = $this->db->prepare($sql_processed['sql']);
		if (!$statement->execute($sql_processed['params'])) { $this->throwPDOError($statement); }

		return $statement->fetchColumn();
    }

    function selectValues($sql, $params = [])
    {
        $sql_processed = $this->processArrayParams($sql, $params);
        $statement = $this->db->prepare($sql_processed['sql']);
        if (!$statement->execute($sql_processed['params'])) { $this->throwPDOError($statement); }
        
        $values = [];
        while ($value = $statement->fetchColumn())
		{
			$values[] = $value;
		}

		return $values;
    }

    function insert($table_name, $data)
    {
        $fields = array_keys($data);
        $params = [];
        $values = [];

        // Build parameters and values for parameterized query
        foreach($data as $key => $value)
        {
            if ($value != 'NOW()')
            {
                $param = ':' . $key;
                $values[$param] = $value;
            }
            else
            {
                $param = $value;
            }
            
            $params[] = $param;
            
        }

        $sql = 'INSERT INTO ' . $table_name . ' (' . implode(', ',$fields) . ') VALUES (' . implode(',', $params) . ')';
        $statement = $this->db->prepare($sql);
        
        if (!$statement->execute($values)) { $this->throwPDOError($statement); }

        return $this->db->lastInsertId();
    }

    function processArrayParams($sql, $params)
    {
        $new_params = [];

        foreach($params as $param => $value)
        {
            if ( is_array($value) && ( stripos($sql, " IN($param)") !== false || stripos($sql, " IN ($param)") !== false) )
            {
                $sql = str_replace(" IN($param)", " IN ($param)", $sql);
                $array_params = [];
                foreach($value as $i => $v)
                {
                    $new_param = $param . '_array_' . $i;
                    $array_params[] = $new_param;
                    $new_params[$new_param] = $v;
                }
                $array_params = implode(',',$array_params);
                $sql = str_replace(" IN ($param)", " IN ($array_params)", $sql);
            }
            else
            {
                $new_params[$param] = $value;
            }
        }
        
        return ['sql' => $sql, 'params' => $new_params];
    }

}