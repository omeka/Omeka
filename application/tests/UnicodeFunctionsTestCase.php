<?php 
require_once HELPERS;	
class UnicodeFunctionsTestCase extends OmekaTestCase
{ 
	function testAllhtmlentities()
	{
		//Should convert single ampersand to entities
		$testAmps = "cookies & cream";
				
		$trans = allhtmlentities($testAmps);
		
		$this->assertEqual($trans,"cookies &amp; cream");
		
		
		//Should convert curly quotes to entities
		$testCurlyQuotes = "“hello there”";
		
		$trans = allhtmlentities($testCurlyQuotes);
								
		$this->assertEqual($trans,"&ldquo;hello there&rdquo;");
		
		
		//Default args should only escape quotes that are outside of the allowed tags
		$testNormalQuotes = '"hello there" <em class="foo">';
		
		$trans = allhtmlentities($testNormalQuotes);
		
		$this->assertEqual($trans,'&quot;hello there&quot; <em class="foo">');
		
		
		//Should convert all other weird unicode to entities
		$testXMLEntities = "©—–&™“”‘’…&";
		
		$trans = allhtmlentities($testXMLEntities);
		
		$this->assertEqual($trans,"&copy;&mdash;&ndash;&amp;&trade;&ldquo;&rdquo;&lsquo;&rsquo;&hellip;&amp;");
		
		//Default args should avoid conversion of <em> tags
		$testEmConversion = "“hello there” <em>dude</em>";
				
		$trans = allhtmlentities($testEmConversion);
				
		$this->assertEqual($trans,"&ldquo;hello there&rdquo; <em>dude</em>", 'allhtmlentities() does not avoid converting <em> tags by default');
		
		//Default args should convert other tags like <div>
		$testDivConversion = "<div>hello there</div>";
		
		$trans = allhtmlentities($testDivConversion);
		
		$this->assertEqual($trans, "&lt;div&gt;hello there&lt;/div&gt;");
		
		//Second arg set to 'false' or 'null' should disallow tags and convert all to entities (for forms)
		$trans = allhtmlentities($testEmConversion,false);
		
		$this->assertEqual($trans,"&ldquo;hello there&rdquo; &lt;em&gt;dude&lt;/em&gt;", 'allhtmlentities() is not converting all tags to entities');
		
		$testSpanConversion = '“hello there” <span class="foo">dude</span>';
		
		$trans = allhtmlentities($testSpanConversion,false);
		
		$this->assertEqual($trans, '&ldquo;hello there&rdquo; &lt;span class=&quot;foo&quot;&gt;dude&lt;/span&gt;');
	}
	
	function testUnescapeAll()
	{
		$test = "“Here is the thing” <em>Test</em><div class=\"foo\">Test</div>";
		
		$trans = allhtmlentities($test);
				
		$unescaped = unescapeAll($trans);
		
		$this->assertEqual($unescaped, '&ldquo;Here is the thing&rdquo; <em>Test</em><div class="foo">Test</div>');
	}
	
	/**
	 * Just for the hell of it, let's make sure this function never gets too slow
	 *
	 **/
/*	function testProfileAllhtmlentities()
	{
		$text = "©—–™“”‘’…";
		$iterations = 100;

		$totalExecution = microtime(true);

		for ($i=0; $i < $iterations; $i++) { 
	
			$trans = allhtmlentities($text);
	
		}

		$totalExecution = microtime(true) - $totalExecution;
		
		$avgTime = $totalExecution / $iterations;
				
		$this->assertTrue( ($avgTime < 0.001) );
		
	} */
}
?>
