<?php

include_once(LOQ_ROOT.'/core/includes/commenthandler.class.php');

class TestComments extends UnitTestCase{
    
    function setUP(){
        $this->db = &new Mockadodb($this);
    }
    /**
    * Moderation test with plain text link
    * We use some of the spam supplied by Xushi (thanks ;))
    */
    function testModerationRaw(){
        $url = '';
    }
    
    /**
    * Moderation test when configured to moderate all
    */
    function testModerationAll(){
        define('C_COMMENT_MODERATION', 'all');
        $url = 'You may find it interesting to check out some information dedicated to <A HREF="http://www.the-discount-store.com/diet-pill.html">phentermine blue diet pills</A>';
        $ch = new commentHandler($this->db);
        $this->assertTrue($ch->needsModeration($url), '');
    }
    
    /**
    * Modertain test when configured to moderate urlonly
    */
    function testModerationUrlonly(){
        define('C_COMMENT_MODERATION', 'urlonly');
        $url = 'You may find it interesting to check out some information dedicated to <A HREF="http://www.the-discount-store.com/diet-pill.html">phentermine blue diet pills</A>';
        $ch = new commentHandler($this->db);
        $this->assertTrue($ch->needsModeration($url), '');
    }
    
    /**
    * Do we work properly with all stats of allowcomments
    */
    function testIsDisabled(){
        $ch = new commentHandler($this->db);
        $this->assertFalse($ch->isDisabled(array('allowcomments' => ''), 'Handle unset value'));
        $this->assertTrue($ch->isDisabled(array('allowcomments' => 'disallow'), 'Handle disallow'));
        $this->assertFalse($ch->isDisabled(array('allowcomments' => 'timed', 'autodisabledate' => time()), 'Handle timed'));
    }
}
?>
