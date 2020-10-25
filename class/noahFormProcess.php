<?php
// $Id: noahFormProcess.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
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
 * NoAh form is a hodge-podge of form functions
 * needs a good review and should probably
 * extend XoopsForm rather than NoAh page ??
 * but it works..
 */
class NoAhFormProcess extends NoAhForm
{
    public function __construct()
    {
    }

    /**
     * @param mixed $post
     * @return mixed
     */

    public function updateItem($post)
    {
        global $xoopsModule;

        if ($this->showVerbose()) {
            echo(': Updating item from ' . $this->group['group_label'] . '<br>');
        }

        $post = self::validatePost($post);

        if (isset($post['error'])) {
            if ($this->showVerbose()) {
                echo(': Error returned <br>');
            }

            return $post;
        }

        switch ($post['submit_func']) {
            case 'submit_new':
                if ($this->showVerbose()) {
                    echo(': Submitting New Item <br>');
                }

                $result = self::addNewRecord($post);

                // check to see if we are adding new table, if so run consume table
                if ($result && 'sysgroups' == $this->group['group_name']) {
                    if ($this->showVerbose()) {
                        echo(': running NoAh Table Consume process <br>');
                    }

                    NoAhTableConsume::tableConsume($result);
                }

                // check to see if we are adding a new field, if so run consume field
                if ($result && 'sysfield' == $this->group['group_name']) {
                    if ($this->showVerbose()) {
                        echo(': running NoAh Field Consume process <br>');
                    }

                    NoAhTableConsume::fieldConsume($result);
                }

                // Grab the id of the created record
                $return = $this->db->getInsertId();

                if ($this->showVerbose()) {
                    echo(": Insert id results = $return");
                }

                break;
            case 'submit_edit':
                if ($this->showVerbose()) {
                    echo(': Editing Item <br>');
                }
                $result = self::editRecord($post);
                // Grab the id affected Rows
                $return = $this->db->getAffectedRows();

                if ($this->showVerbose()) {
                    echo(": Edited Row = $return");
                }

                break;
        }

        // Set the success message. If this isn't set then resort to default

        $success_msg = $post['success_msg'] ?? ($this->group['group_label'] . '<br> ' . _NA_UPDATE_SUCCESS);

        // Set the return path upon success

        // if this isn't provided with the form then set default of noah item view

        // if retstr is set to 'override' then bypass the redirect and just return

        if (!isset($post['retstr'])) {
            $retstr = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/admin/item.php?op=' . $this->group['group_name'] . '&pfunc=open&id=' . $result;

            redirect_header($retstr, 2, $success_msg);
        } elseif ('override' == $post['retstr']) {
            if ($this->showVerbose()) {
                echo(': Override Redirect String <br>');
            }
        } else {
            $retstr = $post['retstr'];

            redirect_header($retstr, 2, $success_msg);
        }

        return $return;
    }

    public function addNewRecord($post)
    {
        global $xoopsModule;

        // Uncomment the following two lines to test process without adding

        // echo('record would have been added, diverted <br>');

        // return;

        $sql = 'INSERT INTO ' . $this->db->prefix($this->group['table_name']);

        $sql .= ' SET ';

        $sql .= self::buildSqlSetStr($post);

        if ($this->showVerbose()) {
            echo("<br>$sql<br>");
        }

        // Run our query. Check to see if post[qf] has been submitted if so

        // the run the Insert using queryF instead of query

        if (isset($post['qf']) && 1 == $post['qf']) {
            if ($this->showVerbose()) {
                echo(': using queryF <br>');
            }

            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            echo($this->db->error() . " <br> $sql");
        } else {
            $result = $this->db->getInsertId();

            if ($this->showVerbose()) {
                echo(": Created Item # $result in group " . $this->group['group_name'] . '<br>');
            }
        }

        return $result;
    }

    public function editRecord($post)
    {
        global $xoopsModule;

        $sql = 'UPDATE ' . $this->db->prefix($this->group['table_name']);

        $sql .= ' SET ';

        $sql .= self::buildSqlSetStr($post);

        $sql .= ' WHERE ' . $this->group['prime'] . " = '" . $post['itemid'] . "'";

        // Run our query. Check to see if post[qf] has been submitted if so

        // the run the Insert using queryF instead of query

        if (isset($post['qf']) && 1 == $post['qf']) {
            if ($this->showVerbose()) {
                echo(': using queryF <br>');
            }

            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        // echo($sql);

        if (!$result) {
            echo($this->db->error() . "<br> $sql");
        } else {
            $result = $this->db->getAffectedRows();

            if ($this->showVerbose()) {
                echo(": Successfully Edited Item $result items in " . $this->group['group_name'] . '<br>');
            }
        }

        return $post['itemid'];
    }

    /**
     * needs review..  :P
     * adds button to $pageform and dumps to template
     * @param mixed $pageform
     * @param mixed $button
     */

    public function formFinalize($pageform, $button = '')
    {
        global $xoopsTpl;

        if (!$button) {
            $button = 'Submit';
        }

        // add a submit button to our form

        $pageform->addElement(new XoopsFormButton('', 'submit', $button, 'submit'));

        // assign form to smarty template

        $pageform->assignByName($xoopsTpl);

        //print '<pre>';
        //	print_r($elements);
        //print '</pre>';
    }

    public function buildSqlSetStr($post)
    {
        $str = '';

        $postcount = count($post);

        $n = 0;

        // loop through all the fields in content group

        foreach ($this->group['field'] as $field) {
            if (isset($post[$field['db_field']])
                || ('checkbox' == $field['fieldtype']
                    && isset($field['show_form'])
                    && !isset($post[$field['db_field']]))) {
                if (!isset($post[$field['db_field']])) {
                    $post[$field['db_field']] = 0;
                }

                if ($n > 0) {
                    $str .= ', ';
                }

                $str .= $field['db_field'] . "='" . $post[$field['db_field']] . "'";

                $n++;
            }
        }

        return $str;
    }

    /**
     * @param mixed $data
     * @return mixed
     */

    public function validatePost($data)
    {
        // loop through all the fields in content group

        foreach ($this->group['field'] as $field) {
            // if field is active in form

            if (isset($field['show_form'])) {
                // convert date select field data to timestamp

                if ('date_select' == $field['fieldtype']) {
                    $data[$field['db_field']] = strtotime($data[$field['db_field']]);
                }

                // convert group select array into serialized data

                if ('group_select' == $field['fieldtype']) {
                    if (!isset($data[$field['db_field']])) {
                        $data[$field['db_field']] = '';
                    }

                    $data[$field['db_field']] = serialize($data[$field['db_field']]);
                }

                // if no field message present create a blank one

                if (!isset($field['error_msg'])) {
                    $field['error_msg'] = '';
                }

                if (isset($field['field_validation'])) {
                    // if field is required as type filled in

                    if ('' != $field['error_msg']
                        && 'filled_in' == $field['field_validation']) {    // if field has not been provided add error message
                        if ('' == $data[$field['db_field']]) {
                            $data['error'][$field['db_field']] = $field['error_msg'];
                        }
                    }

                    // if field is required as type unique value

                    if ('' != $field['error_msg']
                        && 'unique_value' == $field['field_validation']) {
                        $res = self::validateUnique($this->group['table_name'], $field, $data);

                        if (!$res) {
                            $data['error'][$field['db_field']] = $field['error_msg'];
                        }
                    }

                    // if field is required as type email address

                    if ('' != $field['error_msg']
                        && 'email_address' == $field['field_validation']) {
                        $res = self::validate_email($data[$field['db_field']]);

                        if (!$res) {
                            $data['error'][$field['db_field']] = $field['error_msg'];
                        }
                    }
                }
            } #end if ( $field['show_form'] )
        } #end foreach

        return $data;
    }

    public function validateUnique($table, $field, $data)
    {
        if (!$data[$field['db_field']]) {
            return false;
        }

        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix($table);

        $sql .= ' WHERE ' . $field['db_field'] . " = '" . $data[$field['db_field']] . "'";

        if ('submit_edit' == $data['submit_func']) {
            $sql .= ' AND ' . $this->group['prime'] . ' != ' . $data['itemid'];
        }

        if (!$res = $this->db->fetchRow($this->db->query($sql))) {
            echo("Validation Error : <br>$sql<br>" . $this->db->error());
        }

        if (!$res[0]) {
            return true;
        }

        return false;
    }

    public function validate_email($email)
    {
        // Create the syntactical validation regular expression

        $regexp = "^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$";

        // Presume that the email is invalid

        $valid = 0;

        // Validate the syntax

        if (eregi($regexp, $email)) {
            [$username, $domaintld] = preg_split('@', $email);

            // Validate the domain

            if (getmxrr($domaintld, $mxrecords)) {
                $valid = 1;
            }
        } else {
            $valid = 0;
        }

        return $valid;
    }
}
