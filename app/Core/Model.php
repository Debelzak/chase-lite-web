<?php
namespace App\Core;

use App\Core\Database;

class Model
{
    protected function getGameDatabase()
    {
        return new Database(MSSQL_GAME["host"], MSSQL_GAME["username"], MSSQL_GAME["password"], MSSQL_GAME["database"]);
    }

    protected function getWebDatabase()
    {
        return new Database(MSSQL_WEB["host"], MSSQL_WEB["username"], MSSQL_WEB["password"], MSSQL_WEB["database"]);
    }
}