<?
namespace PHPGraphQLServer\DataAccess;

use \PDO;
use \Exception;

class PDOConnection
{
    private static $db = null;

    public static function getDB()
    {
        if (empty(self::$db))
        {
            if (empty($_SERVER['PDO_DSN_STRING']))
            {      
                syslog(LOG_ERR, $e);
            }
            try 
            {
                self::$db = new PDO($_SERVER['PDO_DSN_STRING'], $_SERVER['PDO_USERNAME'], $_SERVER['PDO_PASSWORD']);
            }
            catch (Exception $e)
            {
                syslog(LOG_ERR, $e);
            }
        }
        print_r($db);
        return self::$db;
    }
}