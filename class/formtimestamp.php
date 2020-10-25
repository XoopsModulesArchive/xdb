<?php
// $Id: formtimestamp.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://xoopscube.org>                             //
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

// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://xoopscube.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

/**
 * @author        Kazumi Ono    <onokazu@xoops.org>
 * @copyright     copyright (c) 2000-2005 XOOPS.org
 */

/**
 * A Timestamp Hidden field
 *
 *
 * @author        Kazumi Ono    <onokazu@xoops.org>
 * @copyright     copyright (c) 2000-2005 XOOPS.org
 */
class XoopsFormTimestamp extends XoopsFormElement
{
    /**
     * Value
     * @var    string
     */

    public $_value;

    public $_caption;

    /**
     * Constructor
     *
     * @param string $caption "caption" attribute
     * @param string $name    "name" attribute
     * @param string $value   "value" attribute
     * @param string $option  "all" or "once" attribute
     */

    public function __construct($caption, $name, $value, $option)
    {
        $this->setName($name);

        $this->setHidden();

        $this->setValue($value, $option);

        $this->setCaption($caption);
    }

    /**
     * Get the "value" attribute
     *
     * @return    string
     */

    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets the "value" attribute
     *
     * @patam  $value    string
     * @param mixed $value
     * @param mixed $option
     */

    public function setValue($value, $option)
    {
        if (!$value || 'all' == $option) {
            $value = time();
        }

        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return    string    HTML
     */

    public function render()
    {
        return "<input type='hidden' name='" . $this->getName() . "' id='" . $this->getName() . "' value='" . $this->getValue() . "'> " . date('D M j', $this->getValue());
    }
}
