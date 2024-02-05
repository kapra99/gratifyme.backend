<?php

namespace App\DBAL;

use Doctrine\DBAL\Platforms\MariaDb1027Platform;

class MySQLPlatformDisableForeignKey extends MariaDb1027Platform
{
    /**
     * Disabling the creation of foreign keys in the database (partitioning is used).
     *
     * @return false
     */
    public function supportsForeignKeyConstraints(): bool
    {
        return false;
    }

    /**
     * Disabling the creation of foreign keys in the database (partitioning is used).
     *
     * @return false
     */
    public function supportsForeignKeyOnUpdate(): bool
    {
        return false;
    }
}