<?php
namespace Panadas\AuthModule\Handler;

abstract class AbstractPdo extends AbstractHandler
{

    private $pdo;
    private $tableName;

    public function __construct(\PDO $pdo, $tableName = "authentication")
    {
        $this
            ->setPdo($pdo)
            ->setTableName($tableName);
    }

    public function getPdo()
    {
        return $this->pdo;
    }

    protected function setPdo(\PDO $pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    protected function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }
}
