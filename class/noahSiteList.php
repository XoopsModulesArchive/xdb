<?php
// $Id: noahSiteList.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
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

class NoAhSiteList extends NoAhPage
{
    public $listname;

    public $listid;

    public $listdesc;

    public $listvalues = [];

    public function __construct($name)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->listname = $name;

        $details = $this->getSiteListDetails();

        $this->listid = $details['listid'];

        $this->listdesc = $details['listdesc'];

        $this->listvalues = $this->getSiteListValues();
    }

    public function getSiteListDetails()
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('noah_sitelist') . "
			WHERE listname = '" . $this->listname . "'";

        if (!$res = $this->db->query($sql)) {
            echo($this->db->error() . "<br> $sql <br>");
        } else {
            $row = $this->db->fetchArray($res);
        }

        return $row;
    }

    public function getSiteListValues()
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('noah_sitevalue') . "
			WHERE listid = '" . $this->listid . "'";

        if (!$res = $this->db->query($sql)) {
            echo($this->db->error() . "<br> $sql <br>");
        } else {
            while (false !== ($row = $this->db->fetchArray($res))) {
                $value[] = $row;
            }
        }

        return $value;
    }

    public function siteListNameFromId($id)
    {
        $sql = 'SELECT listname FROM ' . $this->db->prefix('noah_sitelist') . " 
            WHERE listid = '$id'";

        if (!$res = $this->db->query($sql)) {
            echo($this->db->error() . "<br> $sql <br>");
        } else {
            $row = $this->db->fetchArray($res);
        }

        return $row['listname'];
    }

    public function listValues()
    {
        return $this->listvalues;
    }
}
