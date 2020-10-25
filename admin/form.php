<?php
// $Id: form.php,v 1.1 2006/03/27 16:35:58 mikhail Exp $
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
 * NoAh Admin Index
 *
 * main index file for NoAh control panel.
 *
 * @author         fatman < noah.kerkness.ca >
 * @version        0.8
 * @package        NoAh
 */

use Xmf\Request;

require_once 'admin.header.php';
require_once MOD_PATH . '/admin/noah_cptabs.php';

/**
 * used to hold all page data that is collected for display
 * @var    array $post
 */
$page = [];

/**
 * stands for option and is used to hold the requested NoAh Group name
 *
 */
$op = 'sysgroups';

/**
 * stands for 'page function' and is used to hold requested view type
 *
 */
$pfunc = 'form';

/**
 * used to hold an array of submitted data to the page
 *
 */
$post      = [];
$posterror = false;

/**
 * used to pass the name of a field which should have a
 * set value and what that set value should be
 * $var    array $lock
 */
$lock = ['field' => '', 'value' => ''];

/**
 * If $_Get vars are present check for page vars
 */
if (isset($_GET)) {
    if (isset($_GET['op'])) {
        $op = $_GET['op'];
    }
    if (isset($_GET['pfunc'])) {
        $pfunc = $_GET['pfunc'];
    }
    if (isset($_GET['rel'])) {
        $rel = $_GET['rel'];
    }
    if (isset($_GET['id'])) {
        $itemid = $_GET['id'];
    }
    if (isset($_GET['lock'])) {
        $lock['field'] = $_GET['lock'];
    }
    if (isset($_GET['lockv'])) {
        $lock['value'] = $_GET['lockv'];
    }
}

/**
 * If $_POST vars are present check for page vars
 * This should follow collection of $_GET vars as
 * POST vars take priority
 */
if (isset($_POST)) {
    if (isset($_POST['op'])) {
        $op = $_POST['op'];
    }
    if (isset($_POST['pfunc'])) {
        $pfunc = $_POST['pfunc'];
    }
    if (isset($_POST['rel'])) {
        $rel = $_POST['rel'];
    }
    if (isset($_POST['itemid'])) {
        $itemid = $_POST['itemid'];
    }
    if (isset($_POST['lock'])) {
        $lock['field'] = $_POST['lock'];
    }
    if (isset($_POST['lockv'])) {
        $lock['value'] = $_POST['lockv'];
    }
    if (isset($_POST)) {
        $post = $_POST;
    }

    //	print '<pre>';
    //	print_r($post);
    //	print '</pre>';

}

/**
 * If our submitted vars contain a submit_func call
 * then run the NoAh method updateItem.
 */
if (isset($_POST['submit_func'])) {
    $noAh = new NoAhPage($op);
    $post = $noAh->updateItem($_POST);

    if (isset($post['error'])) {
        $posterror = true;
    }
}

/**
 * Set up our admin tabs
 */
$mainTabs->setCurrent('content', 'tabs');

if ($op == 'pages') {
    $mainTabs->setCurrent('pages', 'tabs');
}
if ($op == 'sitelists' || $op == 'sitevalues') {
    $mainTabs->setCurrent('sitelists', 'tabs');
}
if ($op == 'sysprefs' || $op == 'sysprefvalues') {
    $mainTabs->setCurrent('sysprefs', 'tabs');
}

// ********************************************************* admin smarty start
global $xoopsModule;
xoops_cp_header();

require_once XOOPS_ROOT_PATH . '/class/template.php';
if (!isset($xoopsTpl)) { // Just in case, for new releases
    $xoopsTpl  = new XoopsTpl();
    $oldsystem = true;
} else {
    $oldsystem = false;
}

$GLOBALS['xoopsOption']['template_main'] = 'admin/admin.html'; // To be compatible with existing system.
// ********************************************************* admin smarty start

$values       = [];
$noAh         = new NoAhPage($op);
$page['info'] = $noAh->groupSummary();

if (isset($itemid)) {
    $page['info']['itemid'] = $itemid;
}

/**
 * loads an array for default field values
 * @var    array
 */
$values = $noAh->getFieldDefaults();

/**
 * creates an instance of XoopsSimpleForm
 * @name        $pageform
 */
$pageform = new XoopsSimpleForm('Add New ', 'pageform', 'form.php', 'POST');

if ($pfunc == 'edit' && !$posterror) {
    $iteminfo = $noAh->itemDetails($itemid);
    $values   = $iteminfo['item'];
} elseif ($posterror) {
    $values = $post;
}

/**
 * adds elements to pageform
 */
$noAh->getFormView($pageform, $values, $lock);

$page['formextra'] = $noAh->getFormExtra();

if ($pfunc == 'edit') {
    $pageform->addElement(new XoopsFormHidden('submit_func', 'submit_edit'));
    $pageform->addElement(new XoopsFormHidden('itemid', $itemid));
} else {
    $pageform->addElement(new XoopsFormHidden('submit_func', 'submit_new'));
}

$pageform->addElement(new XoopsFormHidden('op', $op));
$pageform->addElement(new XoopsFormHidden('pfunc', $pfunc));

/**
 * Set our return path to our referring page
 */
$pageform->addElement(new XoopsFormHidden('retstr', Request::getString('HTTP_REFERER', '', 'SERVER')));

// set the consume table to true if we need to show
// want to allow user to see the 'consume entire table checkbox'
if ($lock['field'] == 'table_name' && $op == 'sysgroups') {
    $consumebox = new XoopsFormCheckBox(_NA_CHCKBOX_CONSUME_TABLE, 'consumetable', 1);
    $consumebox->addOption('1', 'check this box to automatically add all existing fields with this table');
    $pageform->addElement($consumebox);
}

$noAh->formFinalize($pageform);
$pageform->assignByName($xoopsTpl);

// get our page title before assinging template
$page['title'] = $noAh->getPageTitle();

// assign our page data to template
if (isset($page)) {
    $xoopsTpl->assign('page', $page);
}
$xoopsTpl->assign('tabs', $mainTabs->getSet());

// ********************************************************* admin smarty close
global $xoopsModule;
$xoopsTpl->assign('mod_dir', $xoopsModule->dirname());

if ($oldsystem) { // Don't execute if newer versions has smarty implemented.
    if (isset($xoopsOption['template_main'])) {
        $xoopsTpl->xoops_setCaching(0);
        $xoopsTpl->display('db:' . $xoopsOption['template_main']);
    }
}
xoops_cp_footer();
// ********************************************************* admin smarty close


