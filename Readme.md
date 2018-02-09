# truncateHTML

A PHP function that truncates (shortens) a given HTML5 string to a max number of characters.

__Example:__ truncate after 6 characters including the ellipsis:  
`<p><b>A</b> red ball.</p>` __=>__ `<p><b>A</b> red…</p>`

Compatible with PHP 5.6 and 7+  
Uses the _mbstring_ PHP extension for UTF-8.  
More than 240 unit tests (see or run: [unittest.php](unittest.php))

_The function is in [truncateHTML.php](truncateHTML.php), you can just copy/paste it to your project._


## Features:

- Quickly truncate most common HTML5 sources without using a full HTML parser (which is ~100x slower).
- Configurable ellipsis: `…`, `...`, `<a href="">More</a>`, etc.
  - Can include the length of the ellipsis in the truncated result.
- Supports self-closing tags like: `<img>`, `<img/>`, `<newtag />`
- Collapsing spaces: sequences of multiple spaces are counted only once (including `<br>`, `&nbsp;` and a few others)
- Don't count characters in invisible elements like: `<head>`, `<script>`, `<noscript>`, `<style>`, `<!-- comments -->`
- Supports HTML entities (`&nbsp;`, `&hellip;`, `&quot;`, etc.)
- Whole word: can truncate at the end of the last word instead of cutting in the middle of a word.
  - Cut long words: can truncate in the middle of a word if it is very long (useful to truncate an URL)
- Truncates before the error in case of malformed HTML (like a mismatched closing tag)
- UTF-8 support (multibyte characters)


## Examples: 

```PHP
// Example from the introduction:
truncateHTML(6, "<p><b>A</b> red ball.</p>");
// =>           "<p><b>A</b> red…</p>"

// Whole word:
truncateHTML(5, "<blockquote>A lumberjack</blockquote>");
// =>           "<blockquote>A…</blockquote>"

// Without whole word, without includeEllipsisLength:
truncateHTML(5, "<blockquote>A lumberjack</blockquote>", ['wholeWord' => false, 'includeEllipsisLength' => false]);
// =>           "<blockquote>A lum…</blockquote>"

// Whole word: example of cutting only long words:
truncateHTML( 5, "<a href='https://php.net/docs.php'>https://php.net/docs.php</a>");
// =>            "…"   Notice how wholeWord truncates before opening a tag that would be left empty.
truncateHTML(20, "<a href='https://php.net/docs.php'>https://php.net/docs.php</a>");
// =>            "<a href='https://php.net/docs.php'>https://php.net/doc…</a>"

// Comments, scripts and styles are not counted:
truncateHTML(3, "<script>$();</script><!-- Start div --><div>Hi</div><!-- End div --> More text.");
// =>           "<script>$();</script><!-- Start div --><div>Hi…</div>"

// Collapsing multiple spaces:
truncateHTML(6, "A <br>  &nbsp; \n\t   long space!");
// =>           "A <br>  &nbsp; \n\t   long…"

// Tag mismatch: truncates before the error:
truncateHTML(99, "Click</a>here</a>");
// =>            "Click…"
```


## API:

__`string truncateHTML(int $maxLength, string $html, array $options = [])`__

- `$maxLength`: the returned HTML will contain at most $maxLength countable characters.
  If negative, remove $maxLength countable characters from the end of the $html.
- `$html`: the input HTML string that will be truncated.
- `$options`: (optional) an array of options:

  |Options (with default value)|Descriptions|
  |---|---|
  |`'ellipsis'=>'…'`<br>(or: `'ellipsis'=>'...'`)|The ellipsis that will be included. Can be an empty string, can contain HTML tags.<br>(`'…'` is the horizontal ellipsis character, ie. `'...'` as a single unicode character)<br>(If not using UTF-8 mode, the default value will be `'...'` instead of `'…'`)|
  |`'includeEllipsisLength'=>true`|Whether to include the length of the ellipsis in the length of the truncated result.|
  |`'wholeWord'=>true`|When truncating, don't cut in the middle of a word. Instead cut at the end of the last word.|
  |`'cutWord'=>18`|When `wholeWord` is enabled, allows to cut long words after `cutWord` characters (Set to `0` or `false` to disable)|
  |`'utf8'=>true`|Use UTF-8 mode. You should always use [UTF-8](https://en.wikipedia.org/wiki/UTF-8) though.<br>If `utf8` is `false`, only ASCII-compatible single-byte encodings (such as [Latin-1](https://en.wikipedia.org/wiki/ISO/IEC_8859-1)) are supported. For other encodings, use [mb_convert_encoding](https://secure.php.net/manual/en/function.mb-convert-encoding.php) to convert to UTF-8 and back.<br>(If UTF-8 is disabled, the default ellipsis will be `'...'` instead of `'…'`)|


## Limitations:

XHTML: probably works in most cases, but is untested.

__Not supported:__
- __Malformed HTML__, badly nested tags, missing closing tags: it doesn't try to guess the correct fix (for this you would need a full HTML parser).  
  _Note: when meeting an unexpected closing tag: it always truncates before the closing tag (see the examples)._
- Uncommon HTML code like:
  - [HTML tags inside an HTML Tag attribute](https://stackoverflow.com/questions/4699276/can-data-attribute-contain-html-tags): `<img title="Hello<br>World">`
- The string __`</script>`__ inside `<script>code…</script>`. For this you would need a full HTML parser, or a JavaScript parser. (Other tags are ok, but don't have a closing tag `</script>` in a JavaScript string or comment)
- The string __`</style>`__ inside `<style>code…</style>`. For this you would need a full HTML parser, or a CSS parser. (Other tags are ok, but don't have a closing tag `</style>` in a CSS comment)
- XML
- CDATA ([deprecated in HTML5](https://developer.mozilla.org/en-US/docs/Web/API/CDATASection))

_If you find more, please open an [issue](https://github.com/jlgrall/truncateHTML/issues)._

## History (changelog)

- __v1.0.1__ _(9 Feb. 2018)_:
  - Fix multibyte characters in regex
  - Add parameter types verifications
- __v1.0__ _(5 Feb. 2018)_:
  - Initial version
  - _Inspired by:_
    - _[StackOverflow: Truncate text containing HTML, ignoring tags](https://stackoverflow.com/questions/1193500/truncate-text-containing-html-ignoring-tags/1193598#1193598)_
    - _[truncate() from CakePHP](https://github.com/cakephp/cakephp/blob/master/src/Utility/Text.php)_