<?php
// $Id: item.php,v 1.1 2006/03/27 16:35:58 mikhail Exp $
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
 */
include 'admin.header.php';
include MOD_PATH . '/admin/noah_cptabs.php';

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
$pfunc = 'open';

/**
 * used to hold an array of submitted data to the page
 *
 */
$post = [];
$posterror = false;

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

    if (isset($_POST)) {
        $post = $_POST;
    }
}

/**
 * Set up our admin tabs
 */
$mainTabs->setCurrent('content', 'tabs');
$mainTabs->addSub('addfield', 'form.php?op=sysfield&pfunc=add&lock=groupid&lockv=' . $itemid, _NA_ADD_FIELD, 50, 'content');

if ('pages' == $op) {
    $mainTabs->setCurrent('pages', 'tabs');
}
if ('sitelists' == $op || 'sitevalues' == $op) {
    $mainTabs->setCurrent('sitelists', 'tabs');

    $mainTabs->addSub('addsitevalue', 'form.php?op=sitevalues&pfunc=add&lock=listid&lockv=' . $itemid, _NA_NEW_LIST_VALUE, 50, 'sitelists');
}
if ('sysprefs' == $op || 'sysprefvalues' == $op) {
    $mainTabs->setCurrent('sysprefs', 'tabs');

    $mainTabs->addSub('addprefvalue', 'form.php?op=sysprefvalues&pfunc=add&lock=prefgroupid&lockv=' . $itemid, _NA_NEW_PREF_VALUE, 50, 'sysprefs');
}

// ********************************************************* admin smarty start
global $xoopsModule;
xoops_cp_header();

require_once XOOPS_ROOT_PATH . '/class/template.php';
if (!isset($xoopsTpl)) { // Just in case, for new releases
    $xoopsTpl = new XoopsTpl();

    $oldsystem = true;
} else {
    $oldsystem = false;
}

$GLOBALS['xoopsOption']['template_main'] = 'admin/admin.html'; // To be compatible with existing system.
// ********************************************************* admin smarty start

/**
 * opens a detaled view on a record
 */
$noAh = new NoAhPage($op);
$page = $noAh->itemDetails($itemid);

$noAh->setPageTitle(_NA_TITLE_DETAILS_OF . ' ' . $page['info']['group_label'] . ' ' . _NA_TITLE_ITEM . $page['info']['itemid'] . ' ');
$titlebase = $noAh->getPageTitle();

if (isset($page['relation']) && isset($rel)) {
    // get array key in $page[relation] of selected subpage

    foreach ($page['relation'] as $k => $sub) {
        if ($sub['relationid'] == $rel) {
            $subpage = $sub;

            break;
        }
    }

    $subAh = new NoAhPage($subpage['group_name']);

    $page['sub'] = $subAh->getListData($p, $subpage, '', false);

    $noAh->setPageTitle($titlebase . _NA_TITLE_WITH_RELATION . ' ' . $page['sub']['info']['group_label']);
}

/* Check for detailed template, add folder prefix if assigned */
if ($page['info']['detail_tpl']) {
    $page['info']['detail_tpl'] = 'db:item/' . $page['info']['detail_tpl'];
}

/* Check to see if a sub processing script is assing and include if it is */
if ($page['info']['sub_processor']) {
    require_once MOD_PATH . '/page/' . $page['info']['sub_processor'];
}

/**
 * Making use of NoAh database discovery feature
 *
 * NoAh provides the ability to generate the NoAhGroup
 * and NoAh field settings for any table already in your
 * database.
 *
 * Only provide these features from the sysgroups
 */
if ('sysgroups' == $op) {
    // require_once  $mod_path.'/include/database_discovery.php' ;

    $itemInfo = new NoAhPage($page['item']['group_name']);

    $missingfields = $itemInfo->findMissingFields($itemInfo->group['table_name'], $itemInfo->group['field']);

    // Add some extra values to our sub tab menu

    $tabs['sub']['newfield']['label'] = _NA_ADD_CONTENT_FIELD_TO . ' ' . $itemInfo->group['group_label'];

    $tabs['sub']['newfield']['link'] = XOOPS_URL . '/modules/' . MOD_DIR . '/admin/form.php?op=sysfield&pfunc=add&lock=groupid&lockv=' . $itemInfo->group['groupid'] . '&retstr=/test';

    if ($missingfields) {
        $page['missingfields'] = $missingfields;
    }
}

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
