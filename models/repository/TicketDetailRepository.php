<?php
namespace models\repository;

use PDO;
use Exception;

class TicketDetailRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec("SET NAMES 'utf8'");
    }

}
?>