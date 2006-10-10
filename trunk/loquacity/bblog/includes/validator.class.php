<?php
/**
 * Loquacity - A web blogging application with simplicity in mind - http://www.loquacity.info/
 * Copyright (c) 2006 Kenneth Power
 *
 * @package Loquacity
 * @subpackage Installation
 * @author Kenneth Power <telcor@users.berlios.de>
 * @copyright Copyright &copy; 2006 Kenneth Power
 * @license    http://www.gnu.org/licenses/gpl.html GPL
 * @link http://www.loquacity.info
 * @since 0.8-alpha2
 *
 * LICENSE:
 *
 * This file is part of Loquacity.
 *
 * Loquacity is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Loquacity is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Loquacity; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class validator {
    /**
    * Private
    * @var array stores error messages if not valid
    */
    var $errors;

    /**
    * Constucts a new validator object
    */
    function validator(){
        $this->errors=array();
        $this->validate();
    }

    /**
    * Virtual method
    *
    * @return void
    */
    /*function validate(){
        // Superclass method does nothing
    }*/

    /**
    * Adds an error message to the array
    *
    * @param string $msg the message to add
    * @return void
    */
    function setError($msg){
        $this->errors[]=$msg;
    }

    /**
    * Returns true if string is valid, false if not
    *
    * @return boolean
    */
    function isValid(){
        return (count($this->errors) > 0 ) ? false : true;
    }

    /**
    * Pops the last error message off the array
    *
    * @return string
    */
    function getError(){
        return array_pop($this->errors);
    }
}
