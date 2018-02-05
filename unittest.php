<?php

init();

/* Quick explanation:
   - input("<html>..."): sets the input html string.
   - input(['option' => 'value']): sets or modifies an option that is used for the $options parameter.
   - t($maxLength, "Expected output"): tests the output of truncateHTML() with the expected output, using the given $maxLength and the previously set input html and the $options parameter.
   - sub(['option' => 'value'], function() {...}): sets the given options for inside the function.
*/

sub([
	'ellipsis' => "…",
	'includeEllipsisLength' => false,
	'wholeWord' => false,
	'cutWord' => 3,
	'utf8' => true,
], function() {
	
	/* TEST: empty html */
	input("");
	t(-1, "");
	t( 0, "");
	t( 1, "");
	
	
	/* TEST: basic html */
	input("a");
	t(-1, "…");
	t( 0, "…");
	t( 1, "a");
	t( 2, "a");
	
	input("12 456789");
	t(-2, "12 4567…");
	t(-1, "12 45678…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "12…");
	t( 3, "12 …");
	t( 8, "12 45678…");
	t( 9, "12 456789");
	t(10, "12 456789");
	
	/* TEST: HTML entities */
	input("1&amp; &plus;56789");
	t(-2, "1&amp; &plus;567…");
	t(-1, "1&amp; &plus;5678…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1&amp;…");
	t( 3, "1&amp; …");
	t( 4, "1&amp; &plus;…");
	t( 8, "1&amp; &plus;5678…");
	t( 9, "1&amp; &plus;56789");
	t(10, "1&amp; &plus;56789");
	
	/* TEST: Multiple spaces */
	input("___");
	t(2, "__…");
	input("  &nbsp;\t\t<br> ");
	t(-1, "…");
	t( 0, "…");
	t( 1, "  &nbsp;\t\t<br> ");
	t( 2, "  &nbsp;\t\t<br> ");
	
	input("1 &nbsp; 3456");
	t(-1, "1 &nbsp; 345…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1 &nbsp; …");
	t( 3, "1 &nbsp; 3…");
	t( 4, "1 &nbsp; 34…");
	
	input("1   ");
	t(-1, "1…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1   ");
	
	input("   2");
	t(-1, "   …");
	t( 0, "…");
	t( 1, "   …");
	t( 2, "   2");
	
	
	/* TEST: Include ellipsis length */
	sub(['includeEllipsisLength' => true], function() {
		
		input(['ellipsis' => "…"]);
		
		input("");
		t( 0, "");
		
		input("12");
		t( 0, "…");
		t( 1, "…");
		t( 2, "12");
		
		
		input(['ellipsis' => "..."]);
		
		input("");
		t( 0, "");
		
		input("12");
		t( 0, "...");
		t( 1, "...");
		t( 2, "12");
		
		input("123");
		t( 0, "...");
		t( 1, "...");
		t( 2, "...");
		t( 3, "123");
	});
	
	
	/* TEST: Empty ellipsis */
	sub(['ellipsis' => ""], function() {
		
		input(['includeEllipsisLength' => false]);
		
		input("");
		t( 0, "");
		t( 1, "");
		
		input("abc");
		t( 0, "");
		t( 1, "a");
		t( 2, "ab");
		t( 3, "abc");
		
		input(['includeEllipsisLength' => true]);
		
		input("");
		t( 0, "");
		t( 1, "");
		
		input("abc");
		t( 0, "");
		t( 1, "a");
		t( 2, "ab");
		t( 3, "abc");
	});
	
	
	/* TEST: Whole word */
	sub(['wholeWord' => true], function() {
		
		input("12  45678");
		t( 0, "…");
		t( 1, "…");
		t( 2, "12…");
		t( 3, "12…");
		t( 4, "12…");
		t( 5, "12…");
		t( 6, "12  456…");
		t( 7, "12  4567…");
		t( 7, "12…", ['cutWord' => 0]);
		t( 7, "12…", ['cutWord' => false]);
		
		input("12&nbsp;&nbsp;45678");
		t( 0, "…");
		t( 1, "…");
		t( 2, "12…");
		t( 3, "12…");
		t( 4, "12…");
		t( 5, "12…");
		t( 6, "12&nbsp;&nbsp;456…");
		t( 7, "12&nbsp;&nbsp;4567…");
		
		input("12<br>&nbsp;<br>&nbsp;45678");
		t( 0, "…");
		t( 1, "…");
		t( 2, "12…");
		t( 3, "12…");
		t( 4, "12…");
		t( 5, "12…");
		t( 6, "12<br>&nbsp;<br>&nbsp;456…");
		t( 7, "12<br>&nbsp;<br>&nbsp;4567…");
		
		
		/* TEST: Include ellipsis length */
		sub(['includeEllipsisLength' => true], function() {
			input("12  45678");
			t( 0, "…");
			t( 1, "…");
			t( 2, "…");
			t( 3, "12…");
			t( 4, "12…");
			t( 5, "12…");
			t( 6, "12…");
			t( 7, "12  456…");
			
			/* TEST: HTML entities */
			input("1&amp; &plus;56789");
			t( 0, "…");
			t( 1, "…");
			t( 2, "…");
			t( 3, "1&amp;…");
			t( 4, "1&amp;…");
			t( 5, "1&amp;…");
			t( 6, "1&amp;…");
			t( 7, "1&amp; &plus;56…");
			t( 8, "1&amp; &plus;567…");
			t( 9, "1&amp; &plus;56789");
			t(10, "1&amp; &plus;56789");
		});
	});
	
	
	
	
	/* NOW WITH TAGS */
	
	input("<a />");
	t( 0, "…");
	t( 1, "<a />");
	t( 2, "<a />");
	
	input("<a></a>");
	t( 0, "…");
	t( 1, "<a></a>");
	t( 2, "<a></a>");
	
	input("<img>");
	t( 0, "…");
	t( 1, "<img>");
	t( 2, "<img>");
	
	input("1</a>");	// closing tag mismatch
	t(-1, "…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1…");
	
	input("1<div>2</a></div>");	// closing tag mismatch
	t(-1, "1…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1<div>2…</div>");
	t( 3, "1<div>2…</div>");
	
	input("12<a>34</a>56");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "12…");
	t( 3, "12<a>3…</a>");
	t( 4, "12<a>34…</a>");
	t( 5, "12<a>34</a>5…");
	t( 6, "12<a>34</a>56");
	
	
	/* TEST: Nested tags */
	input("<HTML>1<div attr='val'>2</div>3<aa attr/>4<p >5<bb />6</P>7</html>8");
	t( 0, "…");
	t( 1, "<HTML>1…</html>");
	t( 2, "<HTML>1<div attr='val'>2…</div></html>");
	t( 3, "<HTML>1<div attr='val'>2</div>3…</html>");
	t( 4, "<HTML>1<div attr='val'>2</div>3<aa attr/>4…</html>");
	t( 5, "<HTML>1<div attr='val'>2</div>3<aa attr/>4<p >5…</p></html>");
	t( 6, "<HTML>1<div attr='val'>2</div>3<aa attr/>4<p >5<bb />6…</P></html>");
	t( 7, "<HTML>1<div attr='val'>2</div>3<aa attr/>4<p >5<bb />6</P>7…</html>");
	t( 8, "<HTML>1<div attr='val'>2</div>3<aa attr/>4<p >5<bb />6</P>7</html>8");
	
	
	/* TEST: Self-closing tags */
	input("<!DOCTYPE html5><input><hr/><img src='image.png'>12");
	t( 0, "…");
	t( 1, "<!DOCTYPE html5><input><hr/><img src='image.png'>1…");
	t( 2, "<!DOCTYPE html5><input><hr/><img src='image.png'>12");
	
	
	/* TEST: Tags AND Include ellipsis length */
	sub(['includeEllipsisLength' => true], function() {
		
		input(['ellipsis' => "…"]);
		
		input("12<a>34</a>567");
		t( 0, "…");
		t( 1, "…");
		t( 2, "1…");
		t( 3, "12…");
		t( 4, "12<a>3…</a>");
		t( 5, "12<a>34…</a>");
		t( 6, "12<a>34</a>5…");
		t( 7, "12<a>34</a>567");
		
		
		input(['ellipsis' => "..."]);
		
		input("12<a>34</a>56789");
		t( 0, "...");
		t( 1, "...");
		t( 2, "...");
		t( 3, "...");
		t( 4, "1...");
		t( 5, "12...");
		t( 6, "12<a>3...</a>");
		t( 7, "12<a>34...</a>");
		t( 8, "12<a>34</a>5...");
		t( 9, "12<a>34</a>56789");
	});
	
	
	/* TEST: Don't count spaces separating tags */
	input(" <a> 2 4 </a> <img>   <b>  7  </b> ");
	t( 0, "… ");
	t( 1, " <a> …</a>");
	t( 2, " <a> 2…</a>");
	t( 3, " <a> 2 …</a>");
	t( 4, " <a> 2 4…</a>");
	t( 5, " <a> 2 4 …</a> ");
	t( 6, " <a> 2 4 </a> <img>   <b>  …</b>");
	t( 7, " <a> 2 4 </a> <img>   <b>  7…</b>");
	t( 8, " <a> 2 4 </a> <img>   <b>  7  </b> ");
	
	
	/* TEST: Tags that don't count */
	input("<head>ZZ<title>ZZZ</title></head><noscript>ZZ<a>ZZZ</a></noscript><script defer>alert();</script><style>*{}</style><!-- ZZ -->1");
	t( 0, "…");
	t( 1, "<head>ZZ<title>ZZZ</title></head><noscript>ZZ<a>ZZZ</a></noscript><script defer>alert();</script><style>*{}</style><!-- ZZ -->1");
	
	/* TEST: Tags that don't count before a tag mismatch */
	sub(function() {
		
		input("1<div>2<!----> </a></div>", ['wholeWord' => true]);	// closing tag mismatch
		t(-1, "1…");
		t( 0, "…");
		t( 1, "1…");
		t( 2, "1<div>2…</div>");
		t( 3, "1<div>2…</div>");
		t( 4, "1<div>2…</div>");
	
		input("1<div>2<!----> </a></div>", ['wholeWord' => false]);	// closing tag mismatch
		t(-1, "1…");
		t( 0, "…");
		t( 1, "1…");
		t( 2, "1<div>2…</div>");
		t( 3, "1<div>2…</div>");
		t( 4, "1<div>2…</div>");
	});
	
	/* TEST: Tag mismatch inside a tag that don't count */
	input("1<head>2</a></head>");	// closing tag mismatch
	t(-1, "…");
	t( 0, "…");
	t( 1, "1…");
	t( 2, "1…");
	t( 3, "1…");
	
	/* TEST: Style tag */
	input("<style></style><style> /*</a>--><!--*/</style>1");
	t( 0, "…");
	t( 1, "<style></style><style> /*</a>--><!--*/</style>1");
	
	/* TEST: Script tag */
	input("<script></script><script lang=js>$('</div>'); /*--><!--*/</script>1");
	t( 0, "…");
	t( 1, "<script></script><script lang=js>$('</div>'); /*--><!--*/</script>1");
	
	/* TEST: HTML comment */
	input("<!---->1<!-- ZZ --><!-- </a><!--<script> -->2");
	t( 0, "…");
	t( 1, "<!---->1…");
	t( 2, "<!---->1<!-- ZZ --><!-- </a><!--<script> -->2");
	
	
	/* TEST: Tag AND Whole word */
	sub(['wholeWord' => true], function() {
		input("12<!----><img><a>3456</a><!-- --><img>78901");
		t( 0, "…");
		t( 1, "…");
		t( 2, "12…");
		t( 3, "12…");
		t( 4, "12…");
		t( 5, "12<!----><img><a>345…</a>");
		t( 6, "12<!----><img><a>3456…</a>");
		t( 7, "12<!----><img><a>3456…</a>");
		t( 8, "12<!----><img><a>3456…</a>");
		t( 9, "12<!----><img><a>3456</a><!-- --><img>789…");
		t(10, "12<!----><img><a>3456</a><!-- --><img>7890…");
		t(11, "12<!----><img><a>3456</a><!-- --><img>78901");
		
		
		/* TEST: Include ellipsis length */
		sub(['includeEllipsisLength' => true], function() {
			input("12<!----><img><a>3456</a><!-- --><img>78901");
			t( 0, "…");
			t( 1, "…");
			t( 2, "…");
			t( 3, "12…");
			t( 4, "12…");
			t( 5, "12…");
			t( 6, "12<!----><img><a>345…</a>");
			t( 7, "12<!----><img><a>3456…</a>");
			t( 8, "12<!----><img><a>3456…</a>");
			t( 9, "12<!----><img><a>3456…</a>");
			t(10, "12<!----><img><a>3456</a><!-- --><img>789…");
			t(11, "12<!----><img><a>3456</a><!-- --><img>78901");
			
			/* TEST: HTML entities */
			input("1<b>&amp; &plus;</b><!-- -->567890");
			t( 0, "…");
			t( 1, "…");
			t( 2, "1…");
			t( 3, "1<b>&amp;…</b>");
			t( 4, "1<b>&amp;…</b>");
			t( 5, "1<b>&amp; &plus;…</b>");
			t( 6, "1<b>&amp; &plus;…</b>");
			t( 7, "1<b>&amp; &plus;…</b>");
			t( 8, "1<b>&amp; &plus;</b><!-- -->567…");
			t( 9, "1<b>&amp; &plus;</b><!-- -->5678…");
			t(10, "1<b>&amp; &plus;</b><!-- -->567890");
		});
	});

});



/* TEST: Readme.md examples */

input("<p><b>A</b> red ball.</p>");
t( 6, "<p><b>A</b> red…</p>");

input("<blockquote>A lumberjack</blockquote>");
t( 5, "<blockquote>A…</blockquote>");

input("<blockquote>A lumberjack</blockquote>");
t( 5, "<blockquote>A lum…</blockquote>", ['wholeWord' => false, 'includeEllipsisLength' => false]);

input("<a href='https://php.net/docs.php'>https://php.net/docs.php</a>");
t( 5, "…");
input("<a href='https://php.net/docs.php'>https://php.net/docs.php</a>");
t(20, "<a href='https://php.net/docs.php'>https://php.net/doc…</a>");

input("<script>$();</script><!-- Start div --><div>Hi</div><!-- End div --> More text.");
t( 3, "<script>$();</script><!-- Start div --><div>Hi…</div>");

input("A <br>  &nbsp; \n\t   long space!");
t( 7, "A <br>  &nbsp; \n\t   long…");

input("Click</a>here</a>");
t(99, "Click…");


/* TEST: StackOverflow examples (https://stackoverflow.com/questions/1193500/truncate-text-containing-html-ignoring-tags/48671866#48671866) */

input("<p><b>A</b> red ball.</p>", ['wholeWord' => false]);
t( 9, "<p><b>A</b> red ba…</p>");



finish();






/*############################################################*/
/*############################################################*/
/*############################################################*/
/*###### UTILITY FUNCTIONS ######*/

function init() {
	global $unittest, $paramsStack, $params;
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	ini_set('assert.exception', 1);	// Assertion failure will throw an exception
	
	if (!function_exists('truncateHTML')) {
		require_once('truncateHTML.php');
	}
	
	// SETUP some globals:
	$unittest = [];		// Data related to the executed tests, see definition in init().
	$paramsStack = [];	// Used by sub() to manage $params when changing contexts.
	$params;			// Contains the current parameters for truncateHTML(), see definition in resetParams().
	
	resetParams();
	
	$unittest = [
		'startTime' => microtime(true),
		'succeededTests' => 0,
		'failedTests' => 0,
		'executedTests' => 0,
	];
}

function finish() {
	global $unittest;
	
	$unittest['endTime'] = microtime(true);
	
	echo "\033[01;32mSuccess ({$unittest['succeededTests']}/{$unittest['executedTests']})\033[0m\n";
	echo "Run time: " . round(($unittest['endTime'] - $unittest['startTime']) * 1000, 2) . " ms\n";
}

function resetParams() {
	global $params;
	$params = [
		'html' => '',
		'options' => [],
	];
}


function input($html) {
	global $params, $paramsStack;
	
	$args = func_get_args();
	foreach ($args as $arg) {
		if (is_string($arg)) {
			$params['html'] = $arg;
		}
		else if (is_array($arg)) {
			$params['options'] = $arg + $params['options'];
		}
	}
}

function sub() {
	global $params, $paramsStack;
	
	$paramsStack[] = $params;
	
	$args = func_get_args();
	foreach ($args as $arg) {
		if (is_string($arg)) {
			$params['html'] = $arg;
		}
		else if (is_array($arg)) {
			$params['options'] = $arg + $params['options'];
		}
		else if (is_callable($arg)) {
			$arg();
		}
	}
	
	$params = array_pop($paramsStack);
}

function t($maxLength, $expect, array $options = []) {
	global $unittest, $params;
	
	$html = $params['html'];
	$options = $options + $params['options'];
	$out = truncateHTML($maxLength, $html, $options);
	$unittest['executedTests']++;
	if ($out !== $expect) {
		$unittest['failedTests']++;
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$line = $trace[0]['line'];
		echo "\033[01;31mFailed test {$unittest['executedTests']} (line $line):\033[0m\n";
		echo "maxLength: $maxLength\n";
		echo "html:     '$html'\n";
		echo "output:   '$out'\n";
		echo "expected: '$expect'\n";
		echo "options: ".var_export($options, true)."\n";
		exit();
	}
	else {
		$unittest['succeededTests']++;
	}
}