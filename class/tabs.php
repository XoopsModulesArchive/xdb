<?php
// $Id: tabs.php,v 1.1 2006/03/27 16:36:00 mikhail Exp $
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
 * Class XoopsTabs is a simple class that lets you build tab menus
 * @author    fatman    email: noah >4T< kerkness >D0T< ca
 */
class XoopsTabs
{
    /**
     * an array of tabs
     * @var array
     */

    public $tabs = [];

    /**
     * an array of sublinks to display under an active tab
     * @var array
     */

    public $subs = [];

    /**
     * the name of the current tab
     * @var string
     */

    public $current_tab;

    /**
     * the name of the current sub link
     * @var string
     */

    public $current_sub;

    /**
     * This is a name used in your style sheet
     * @var string
     */

    public $_style;

    /**
     * set to true if build in style sheet is to be used
     * @var bool
     */

    public $_return_style = true;

    /**
     * Provides a way to add some text to far right side of tabs
     * @var string set this to plain text to show far right side of tabs
     */

    public $_righttxt = 'XoopsTabs';

    /**
     * XoopsTabs class constructor
     * @param mixed $style
     */

    public function __construct($style = 'xtabs')
    {
        $this->_style = $style;

        if ('xtabs' != $this->_style) {
            $this->_return_style = false;
        }
    }

    /**
     * Returns array of tabs
     * @param void
     * @return array
     * @return array
     */

    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * Returns array of sub tabs
     * @param void
     * @return array
     * @return array
     */

    public function getSubs()
    {
        return $this->subs;
    }

    /**
     * Returns a multidimensional array of tabs with active tab info and current sub links
     * @param void
     * @return array
     * @return array
     */

    public function getSet()
    {
        return $this->fetchTabSet();
    }

    /**
     * Print the tabs to the browser
     */

    public function display()
    {
        print $this->render();
    }

    /**
     * Assigns the html for all tabs to a single smarty tag.
     */

    public function assign()
    {
        global $xoopsTpl;

        $xoopsTpl->assign($this->_style, $this->render());
    }

    /**
     * Method for setting the current tab or sub tab
     * @param string $name name of the current link
     * @param string $set  either 'tabs' or 'subs'
     */

    public function setCurrent($name, $set = 'tabs')
    {
        if ('tabs' == $set) {
            $this->current_tab = $name;
        }

        if ('subs' == $set) {
            $this->current_sub = $name;
        }
    }

    /**
     * Returns the name of the current tab
     * @return string
     */

    public function getCurrent()
    {
        return $this->current_tab;
    }

    /**
     * Method to add a single tab
     * @param string $name   a unique name for your link
     * @param string $link   the url for your link
     * @param string $label  the text to display for link
     * @param int    $weight the display order ****   <--  doesn't do anything yet
     */

    public function addTab($name, $link, $label, $weight = 10)
    {
        $this->addSet('tabs', $name, $link, $label, $weight);
    }

    /**
     * Method to add multiple tabs from an array of data
     * @param array $tabs
     */

    public function addTabArray($tabs)
    {
        foreach ($tabs as $name => $tab) {
            $this->addSet('tabs', $name, $tab['link'], $tab['label'], $tab['weight']);
        }
    }

    /**
     * Method to add a single sub link for display below an active tab
     * @param string $name   a unique name for your link
     * @param string $link   the url for your link
     * @param string $label  the text to display for link
     * @param string $weight the display order ****   <--  doesn't do anything yet
     * @param string $parent the name of the tab which this sublink should display under
     */

    public function addSub($name, $link, $label, $weight, $parent)
    {
        $this->addSet('subs', $name, $link, $label, $weight, $parent);
    }

    /**
     * Method to add multiple sub links from an array of data
     * @param array $subs
     */

    public function addSubArray($subs)
    {
        foreach ($subs as $name => $sub) {
            $this->addSet('subs', $name, $tab['link'], $tab['label'], $tab['weight']);
        }
    }

    /**
     * Method is used by the addTab and addSub methods and should not be called directly
     * @param mixed $set
     * @param mixed $name
     * @param mixed $link
     * @param mixed $label
     * @param mixed $weight
     * @param null|mixed $parent
     */

    public function addSet($set, $name, $link, $label, $weight, $parent = null)
    {
        if ('tabs' == $set) {
            $this->tabs[$name]['link'] = $link;

            $this->tabs[$name]['label'] = $label;

            $this->tabs[$name]['weight'] = $weight;

            $this->tabs[$name]['name'] = $name;
        } elseif ('subs' == $set) {
            $this->subs[$parent][$name]['link'] = $link;

            $this->subs[$parent][$name]['label'] = $label;

            $this->subs[$parent][$name]['weight'] = $weight;

            $this->subs[$parent][$name]['name'] = $name;
        }
    }

    /**
     * Method is used to clear all assigned sub links
     * @param void
     */

    public function clearSubs()
    {
        $this->subs = null;
    }

    /**
     * Method is used to build a complete set of data which can then be easily used
     * to display tabs in a webpage. This method should not be called directly and
     * can be accessed via the getSet() method.
     * @return  array   full tab data and sub links for active tab
     */

    public function fetchTabSet()
    {
        $set['tabs'] = $this->tabs;

        $subs = $this->subs;

        foreach ($subs as $k => $v) {
            if ($k == $this->current_tab) {
                $set['subs'] = $v;
            }
        }

        if (isset($this->current_tab)) {
            $set['tabs'][$this->current_tab]['current'] = 1;
        }

        if (isset($this->current_sub)) {
            $set['subs'][$this->current_sub]['current'] = 1;
        }

        $set['tabcount'] = count($set['tabs']);

        if (isset($set['subs'])) {
            $set['subcount'] = count($set['subs']);
        }

        return $set;
    }

    /**
     * Return the html which makes up the tabs.
     */

    public function render()
    {
        $html = '';

        if ($this->_return_style) {
            $html .= $this->getStyle();
        }

        $tabs = $this->getSet();

        $html .= "<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n
		  <tr>\n
			<td><div id='" . $this->_style . "'>\n
		  <ul>\n";

        foreach ($tabs['tabs'] as $k => $tab) {
            $html .= '<li';

            if (1 == $tab['current']) {
                $html .= " id='current'";
            }

            $html .= "><a href='" . $tab['link'] . "'>" . $tab['label'] . "</a></li>\n";
        }

        $html .= "<li id='rightside'>" . $this->_righttxt . "</li>\n
	 	  </ul>\n
		</div></td>\n
		  </tr>\n
		  <tr>\n
			<td height='30'>\n
			<div>&nbsp; &nbsp;";

        $n = 0;

        foreach ($tabs['subs'] as $k => $sub) {
            if ($n > 0) {
                $html .= '| &nbsp;';
            }

            $html .= "<a href='" . $sub['link'] . "'>" . $sub['label'] . '</a> &nbsp;';

            $n++;
        }

        $html .= "</div>\n
			</td>\n
		  </tr>\n
		</table>";

        return $html;
    }

    /**
     * Create a default style sheet
     */

    public function getStyle()
    {
        $style = "<style type='text/css' media='screen'>
		#xtabs {
		  float:left;
		  width:100%;
		  background:#DAE0D2 url('" . XOOPS_URL . "/images/bg.gif') repeat-x bottom;
		  font-size:93%;
		  line-height:normal;
		  }		\r\n
		#xtabs ul {
		  margin:0;
		  padding:10px 10px 0;
		  list-style:none;
		  }		\r\n
		#xtabs li {
		  float:left;
		  background:url('" . XOOPS_URL . "/images/left.gif') no-repeat left top;
		  margin:0;
		  padding:0 0 0 9px;
		  list-style:none;
		 }		\r\n
		#xtabs a {
		  float:left;
		  display:block;
		  background:url('" . XOOPS_URL . "/images/right.gif') no-repeat right top;
		  padding:5px 15px 4px 6px;
		  text-decoration:none;
		  font-weight:bold;
		  color:#765;
		  }		\r\n
		/* Commented Backslash Hack
		   hides rule from IE5-Mac \*/  		\r\n
		#xtabs a {float:none;} 		\r\n
		/* End IE5-Mac hack */ 		\r\n
		#xtabs a:hover {
		  color:#333;
		  }		\r\n
		#xtabs #current {
		  background-image:url('" . XOOPS_URL . "/images/left_on.gif');
		  }		\r\n
		#xtabs #current a {
		  background-image:url('" . XOOPS_URL . "/images/right_on.gif');
		  color:#333;
		  padding-bottom:5px;
		  }		\r\n
		#xtabs #rightside {
		  float:right;
		  background:none;
		  }		\r\n
		</style>
	";

        return $style;
    }
} // END CLASS
