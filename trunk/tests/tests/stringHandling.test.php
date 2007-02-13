<?php

include_once(LOQ_ROOT.'/core/includes/stringhandler.class.php');

class TestStringhandler extends UnitTestCase{
    
    /**
    * A series of tests to ensure we are detecting all links
    * Some links from Xushi's SPAM harvest ;)
    */
    function testContainsLinks(){
        $uri = 'You may find it interesting to check out some information dedicated to <A HREF="http://www.the-discount-store.com/diet-pill.html">phentermine blue diet pills</A>';
        $this->assertTrue(stringhandler::containsLinks($uri));
        $uri = 'Take your time to take a look at some relevant information on <A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A> <A HREF="http://www.poker-new.com/video-poker-game.html">video poker game</A>';
        $this->assertTrue(stringhandler::containsLinks($uri));
        $uri = 'mailto:kenneth.power@gmail.com';
        $this->assertTrue(stringhandler::containsLinks($uri));
        $uri = 'www.google.com';
        $this->assertTrue(stringhandler::containsLinks($uri));
        $uri = 'ftp.tel-cor.com';
        $this->assertTrue(stringhandler::containsLinks($uri));
        $uri = 'ftp://tel-cor.com';
        $this->assertTrue(stringhandler::containsLinks($uri));
    }
    /**
    * Send a block of text to transformLinks
    */
    function testTransformLinksBlock(){
        $uri = '<strong>Web Clips:</strong> Everybody seems to want to call RSS feeds something other than RSS feeds; in the Sidebar, they\'re Web Clips. (Which is not standard Google nomenclature--the <a target="_blank" href="http://www.google.com/ig">Google Personalized Homepage</a> calls them "feeds," and <a href="http://news.google.com">Google News</a> knows them as "RSS.") The Web Clips tool watches where you browse on the Web, and as you come across pages with feeds, it adds them to its list. I\'m not sure how I feel about this yet--I travel through a <em>lot</em> of sites in any given day, and the fact I\'ve landed somewhere that has a feed doesn\'t necessarily mean I want it delivered to my desktop. But you can delete feeds, move them around, or add your own, so it\'s possible to use Web Clips as a micro-RSS reader.';
        $conv = stringhandler::transformLinks($uri);
        $this->assertEqual($conv, '<strong>Web Clips:</strong> Everybody seems to want to call RSS feeds something other than RSS feeds; in the Sidebar, they\'re Web Clips. (Which is not standard Google nomenclature--the <a target="_blank" href="http://www.google.com/url?sa=D&q=http://www.google.com/ig">Google Personalized Homepage</a> calls them "feeds," and <a href="http://www.google.com/url?sa=D&q=http://news.google.com">Google News</a> knows them as "RSS.") The Web Clips tool watches where you browse on the Web, and as you come across pages with feeds, it adds them to its list. I\'m not sure how I feel about this yet--I travel through a <em>lot</em> of sites in any given day, and the fact I\'ve landed somewhere that has a feed doesn\'t necessarily mean I want it delivered to my desktop. But you can delete feeds, move them around, or add your own, so it\'s possible to use Web Clips as a micro-RSS reader.');
    }
    
    /**
    * Test transformLinks
    */
    function testTransformLinks(){
        //Existing tags
        $uri = '<A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A>';
        $conv = stringhandler::transformLinks($uri);
        $this->assertNotEqual($uri, $conv, 'TestTransformLinks: unable to work with existing tag ([%s])');
        $this->assertEqual($conv, '<a href="http://www.google.com/url?sa=D&q=http://www.poker-new.com/free-poker-games.html">free poker games</a>');
        //raw text
        $uri = 'http://www.poker-new.com/free-poker-games.html';
        $conv = stringhandler::transformLinks($uri);
        $this->assertNotEqual($uri, $conv, 'TestTransformLinks: failed transforming raw text ([%s])');
        $this->assertEqual($conv, '<a href="http://www.google.com/url?sa=D&q=http://www.poker-new.com/free-poker-games.html">www.poker-new.com/free-poker-games.html</a>');
        //No protocol
        $uri = 'www.poker-new.com/free-poker-games.html';
        $conv = stringhandler::transformLinks($uri);
        $this->assertNotEqual($uri, $conv, 'TestTransformLinks: failed protocol test ([%s])');
        $this->assertEqual($conv, '<a href="http://www.google.com/url?sa=D&q=http://www.poker-new.com/free-poker-games.html">www.poker-new.com/free-poker-games.html</a>');
        //email
        /*$uri = 'mailto:me@example.com';
        $conv = stringhandler::transformLinks($uri);
        $this->assertNotEqual($uri, $conv, 'TestTransformLinks: failed email test ([%s])');
        $this->assertEqual($conv, '<a href="mailto:me@example.com">me</a>', 'TestTransformLinks: unable to create email link');*/
        $uri = 'http://www.google.com';
        $this->assertEqual(stringhandler::transformLinks($uri, true), '<a href="http://www.google.com/url?sa=D&q=http://www.google.com">www.google.com</a>');
        //now something a bit more complex
        $uri = 'http://www.google.com/search?q=test+case+is+stupid&sourceid=mozilla-search&start=0&start=0&ie=utf-8&oe=utf-8&client=firefox-a&rls=org.mozilla:en-US:official';
        $this->assertTrue(stringhandler::transformLinks($uri, true), '<a href="http://www.google.com/url?sa=D&q=http://www.google.com/search?q=test+case+is+stupid&sourceid=mozilla-search&start=0&start=0&ie=utf-8&oe=utf-8&client=firefox-a&rls=org.mozilla:en-US:official">www.google.com/search?q=test+case+is+stupid&sourceid=mozilla-search&start=0&start=0&ie=utf-8&oe=utf-8&client=firefox-a&rls=org.mozilla:en-US:official</a>');
        $uri = 'www.aol.com';
        $this->assertEqual(stringhandler::transformLinks($uri), '<a href="http://www.google.com/url?sa=D&q=http://www.aol.com">www.aol.com</a>');
    }
    
    /**
    *
    */
    function testRemoveTags(){
        $uri = 'Take your time to take a look at some relevant information on <A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A> <A HREF="http://www.poker-new.com/video-poker-game.html">video poker game</A>';
        $conv = stringhandler::removeTags($uri);
        $this->assertNotEqual($uri, $conv);
        $this->assertEqual($conv, 'Take your time to take a look at some relevant information on free poker games video poker game');
    }
    /**
    *
    */
    function testEncodeHtml(){
        $uri = 'Take your time to take a look at some relevant information on <A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A> <A HREF="http://www.poker-new.com/video-poker-game.html">video poker game</A>';
        $conv = stringhandler::encodeHTML($uri);
        $this->assertNotEqual($uri, $conv);
        $this->assertEqual($conv, 'Take your time to take a look at some relevant information on &lt;A HREF=&quot;http://www.poker-new.com/free-poker-games.html&quot;&gt;free poker games&lt;/A&gt; &lt;A HREF=&quot;http://www.poker-new.com/video-poker-game.html&quot;&gt;video poker game&lt;/A&gt;');
    }
    
    /**
    * Google redirect tests
    */
    function testGoogleRedirect(){
        $uri = 'Take your time to take a look at some relevant information on <A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A> <A HREF="http://www.poker-new.com/video-poker-game.html">video poker game</A>';
        $conv = stringhandler::redirectHref($uri);
        $this->assertNotEqual($uri, $conv, 'Google HREF transform: The BEFORE and AFTER URIs match, transform did not occur.');
        $this->assertEqual($conv, 'Take your time to take a look at some relevant information on <A href="http://www.google.com/url?sa=D&q=http://www.poker-new.com/free-poker-games.html">free poker games</A> <A href="http://www.google.com/url?sa=D&q=http://www.poker-new.com/video-poker-game.html">video poker game</A>', 'Google HREF Transform:  redirect not applied,');
        $uri = 'Take your time to take a look at some relevant information on http://www.poker-new.com/free-poker-games.html http://www.poker-new.com/video-poker-game.html';
        $conv = stringhandler::redirectUrl($uri);
        $this->assertNotEqual($uri, $conv, 'Google URL transform: The BEFORE and AFTER URIs match, transform did not occur.');
        $this->assertEqual($conv, 'Take your time to take a look at some relevant information on http://www.google.com/url?sa=D&q=http://www.poker-new.com/free-poker-games.html http://www.google.com/url?sa=D&q=http://www.poker-new.com/video-poker-game.html', 'Google URL Transform:  redirect not applied,');
    }
    
    function testContainsTransformedLinks(){
        $uri = 'Take your time to take a look at some relevant information on <A HREF="http://www.poker-new.com/free-poker-games.html">free poker games</A> <A HREF="http://www.poker-new.com/video-poker-game.html">video poker game</A>';
        $this->assertTrue(stringhandler::containsTransformedLinks($uri));
    }
}
?>