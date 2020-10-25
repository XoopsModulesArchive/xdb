<?php

// $Id: generic_form.php,v 1.1 2006/03/27 16:36:23 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                No-Ah - PHP Content Architecture System                    //
//                    Copyright (c) 2004 KERKNESS.CA                         //
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
 * A Demo Signup Form which can be used to for any No-Ah content group
 *
 * Written originally for No-Ah 0.8.7
 *
 * This file demonstrates how No-Ah handles forms. This file should be
 * located in the folder xoopsroot/modules/noah/page/   and it should
 * be assigned to a Mod Page using the No-Ah control panel.
 *
 * The script here will execute every time the Mod Page is displayed.
 * We are also going to submit our form to this page as well, so it will
 * also be called everytime the your form is submitted.
 *
 * This code contains a few chunks of functionality which will get executed
 * depending on how the script is being called. For example one chunk will
 * execute only when the form is submitted others may run every time the page
 * is called.
 *
 * Assumptions: You've created a content group and added several fields to it
 * if you haven't done this please see the HowTo documents at noah.tetrasi.com
 *
 * @author  fatman
 * @version 1.0
 */

/**
 * **************** DECLARE SCRIPT VARS **********************************
 */

/**
 * Create our main No-Ah object for our content group
 */

use Xmf\Request;

$myGroup = new NoAhPage('group_navname');

/**
 * $values
 *
 * an array of data which will hold the values for our form field. This array
 * will be added to or overwritten depending on the state of our form.
 *
 * @var    array
 */
$values = [];

/**
 * $post
 *
 * an array which will contain posted data and also track results of that post.
 *
 * @var    array
 */
$post = [];

/**
 * **************** SET THE PAGE FUNCTION  *****************************
 *
 * Set the function for this page in variable $pfunc
 *
 * For our form we are going to accept 2 types of functions. An ADD function and
 * and EDIT function. One will tell the script we want to build a form to edit
 * an existing content record, and the other that we want to add a new record.
 *
 * If the value of $pfunc is EDIT then you must also set the $itemid variable.
 * This is the id number of the No-Ah content record you want to edit.
 *
 * You can define $pfunc any way you want. I typically pass a value in $_GET.
 */

// if no value for pfunc is provided set pfunc to add
$pfunc = ($_GET['pfunc'] ?? 'add');

// if $pfunc == edit set page title to Edit, else set our page func to add
if ('edit' == $pfunc) {
    // set a title for our form

    $form_title = 'Edit ' . $myGroup->group['group_label'];

    // set the itemid

    $itemid = $_GET['itemid'];
} else {
    // set a title for our form

    $form_title = 'Add ' . $myGroup->group['group_label'];
}

/**
 * **************** ACCEPTING FORM SUBMISSIONS *****************************
 *
 * Let's check to see if the form has been submitted and if it has, we will
 * update the database with the content.
 *
 * A successful submission will result in a redirect unless you have set the
 * redirection parameter $_POST['retstr'] to 'override'
 *
 * If $_POST['retstr'] has been set to 'override' then the results of updateItem
 * will be the new ID key for the content record which was created. Or it will
 * return true if an existing item was updated.
 *
 * IF the form submission doesn't pass the validation settings in for our content
 * fields then updateItem will return a full set of posted data along with error
 * details. If that is the case we will just let our script carry on. We call this
 * before we load the actual form so that we may display error info along with
 * our form, should there be an error.
 */
if (isset($_POST['submit_func'])) {
    $post = $myGroup->updateItem($_POST);
}

/**
 * **************** DISPLAYING THE FORM  ***********************************
 *
 * When loading the form we have to go through a couple of steps
 *
 * - set default values to our form based on pfunc and error status
 * - set form fields for the form
 * - set hidden parameter fields for the form
 * - assign the form to our template
 */

/**
 * Fill our $values array for form display
 */
if ('edit' == $pfunc && !$post['error']) {
    // load the details of our item for the form

    $iteminfo = $myGroup->itemDetails($itemid);

    // assing all the item details to our $values array

    $values = $iteminfo['item'];
} elseif ($post['error']) {
    // If our post had errors then assign our post data

    // to our values array

    $values = $post;
} else {
    // Else set the default values for our form, as defined

    // in the no-ah admin for our content fields

    $values = $myGroup->getFieldDefaults();
}

/**
 * creates an instance of XoopsSimpleForm
 * @param Form title
 * @param Form name
 * @param Form action
 * @param Form method
 */
$action_str = $_SERVER['REQUEST_URI'];
$myForm = new XoopsSimpleForm($form_title, 'myform', $action_str, 'POST');

/**
 * Optional field lock
 *
 * If you want to force a value for a field you can use the No-Ah method
 * addLock or addLockArray
 */
// $myGroup->addLock ('account_name', 'force this name' )

/**
 * Add our content fields to our form object
 */
$myGroup->getFormView($myForm, $values);

/**
 * No-Ah provides more details about a form than what XoopsForms supports.
 * Ideally this next method call should extend the one above, but at the moment
 * it doesn't.
 *
 * Here we assign the extra form data to the no-ah $page array for later access in
 * our template.
 */
$page['formextra'] = $myGroup->getFormExtra();

/**
 * Every time No-Ah is handling form submissions it needs some required information
 * in order to handle the results. Here we set these parameters in hidden form fields
 * so that they will be sent along with out POST data.
 *
 * These are set differently depending on the current $pfunc value
 */
if ('edit' == $pfunc) {
    // set a submission function in a hidden field

    $myForm->addElement(new XoopsFormHidden('submit_func', 'submit_edit'));

    // assign our itemid to a hidden field

    $myForm->addElement(new XoopsFormHidden('itemid', $itemid));
} else {
    // set a submission function in a hidden field

    $myForm->addElement(new XoopsFormHidden('submit_func', 'submit_new'));
}

// set our content group name to a hidden field
$myForm->addElement(new XoopsFormHidden('op', $myGroup->group['group_name']));

/**
 * Set our return path to a hidden field.
 *
 * The return path will be the URL we send the user to ONLY if our form submission
 * is successful. You can set this to any page you want. If you DON'T want to send
 * the user to a new location after a successful submission then set this hidden
 * field to 'override'
 *
 * I normally set this to the last page the user visited before they loaded the
 * form.
 */
$myForm->addElement(new XoopsFormHidden('retstr', Request::getString('HTTP_REFERER', '', 'SERVER')));

/**
 * Assign our form to our template and label our submit button.
 */
$myGroup->formFinalize($myForm, 'Submit');
