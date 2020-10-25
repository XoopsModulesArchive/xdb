<?php
// $Id: noahloader.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
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

/**
 * noahloader.php can be used to gain access to complete set of
 * NoAh class files from other modules or blocks
 */
require_once __DIR__ . '/noahPage.php';
require_once __DIR__ . '/noahCommon.php';
require_once __DIR__ . '/noahGroup.php';
require_once __DIR__ . '/noahField.php';
require_once __DIR__ . '/noahForm.php';
require_once __DIR__ . '/noahList.php';

require_once __DIR__ . '/noahFormProcess.php';
require_once __DIR__ . '/noahFormDirSelect.php';
require_once __DIR__ . '/noahTableConsume.php';
require_once __DIR__ . '/noahDbDiscovery.php';
require_once __DIR__ . '/noahListForm.php';
require_once __DIR__ . '/noahSitePage.php';
require_once __DIR__ . '/noahSiteList.php';



