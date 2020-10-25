<?php
// $Id: db_remote.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                No-Ah - PHP Content Architecture Stem                      //
//                    Copyright (c) 2004 KERKNESS.C                          //
//                       <http://noah.tetrasi.com>                          //
//                          A XOOPS.org Module                               //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Ryan Mayberry AKA (fatman / kerkness)                             //
// URL: http://noah.tetrasi.com/                                             //
// Thanks: To everyone at xoops.org, dev,xoops and all No-Ah developers      //
// Project: The No-Ah Project                                                //
// ------------------------------------------------------------------------- //

/*

* Database class for connecting to glb databases

* It extends xoops object.

*/

class myDB extends XoopsObject
{
    /**
     * @var
     */

    public $host = '';

    /**
     * @var
     */

    public $user = '';

    /**
     * @var
     */

    public $password = '';

    /**
     * @var
     */

    public $database = '';

    /**
     * @var
     */

    public $persistent = false;

    /**
     * @var
     */

    public $conn = null;

    /**
     * @var
     */

    public $result = false;

    /**
     * @param string $host       database server
     * @param string $user       database user
     * @param string $password   database user pass
     * @param string $database   database name
     * @param bool   $persistent set to true to use a persistent db connection
     */

    public function __construct($host, $user, $password, $database, $persistent = false)
    {
        $this->host = $host;

        $this->user = $user;

        $this->password = $password;

        $this->database = $database;

        $this->persistent = $persistent;
    }

    public function myTest()
    {
        return 'test';
    }

    /**
     * Open the the database connection
     */

    public function dbOpen()
    {
        /* Choose the right connectoin type */

        if ($this->persistent) {
            $func = 'mysql_pconnect';
        } else {
            $func = 'mysql_connect';
        }

        /* Connect to Mysql Server */

        $this->conn = $func($this->host, $this->user, $this->password);

        if (!$this->conn) {
            return false;
        }

        /* Select database */

        if (@!mysqli_select_db($GLOBALS['xoopsDB']->conn, $this->database, $this->conn)) {
            return false;
        }

        return true;
    }

    /**
     * Close the database connection
     */

    public function dbClose()
    {
        return (@$GLOBALS['xoopsDB']->close($this->conn));
    }

    /**
     * Function for returning database errors
     */

    public function dbError()
    {
        return ($GLOBALS['xoopsDB']->error());
    }

    /**
     * Function to query the database
     * @param mixed $sql
     * @return bool
     * @return bool
     */

    public function dbQuery($sql)
    {
        $this->result = @$GLOBALS['xoopsDB']->queryF($sql, $this->conn);

        return (false !== $this->result);
    }

    /**
     * Function to get affected rows from query results
     */

    public function dbAffectedRows()
    {
        return (@$GLOBALS['xoopsDB']->getAffectedRows($this->conn));
    }

    /**
     * Function to get affected rows from query results
     */

    public function dbInsertId()
    {
        return (@$GLOBALS['xoopsDB']->getInsertId($this->conn));
    }

    /**
     * function to return number of rows in a query results
     */

    public function dbNumRows()
    {
        return (@$GLOBALS['xoopsDB']->getRowsNum($this->result));
    }

    /*

    * functions to return result set

    */

    public function dbFetchObject()
    {
        return (@$GLOBALS['xoopsDB']->fetchObject($this->result, MYSQL_ASSOC));
    }

    public function dbFetchArray()
    {
        return (@$GLOBALS['xoopsDB']->fetchBoth($this->result, MYSQL_NUM));
    }

    public function dbFetchRow()
    {
        return (@$GLOBALS['xoopsDB']->fetchRow($this->result));
    }

    public function dbFetchAssoc()
    {
        return (@$GLOBALS['xoopsDB']->fetchArray($this->result));
    }

    /**
     * Function to free result set
     */

    public function dbFreeResult()
    {
        return (@$GLOBALS['xoopsDB']->freeRecordSet($this->result));
    }
}



