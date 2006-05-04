<?php

/**
 * File: comments.class.php
 * Author: Kenneth Power <kenneth.power@gmail.com>
 * Date: May 4, 2006
 * 
 * A class to manage all comment related functions
 */
class comments {

    var $_post = null;
    /**
     * Constrcutor
     * Supplying a postid means we are working with a saved post
     */
    function comments(&$db, $postid=null) {
        if(!isnull($postid)){
            $this->_post = $postid;
            $sql = 'SELECT * from `".T_COMMENTS."` WHERE postid='.$this->_post;
        }
    }
    /**
     * Retrieve all comments for the specific post
     */
    function getComments($postid=null){
        if(!is_null($postid)){
            return $this->_comments;
        }
    }
    function getComment($cid=0){
        if($cid > 0){
            if(array_key_exists($cid, $this->_comments))
                return $this->_comments[$cid];
            else
                return;
        }
    }
}
?>