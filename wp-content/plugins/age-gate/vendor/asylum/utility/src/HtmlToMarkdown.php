<?php

namespace Asylum\Utility;

class HtmlToMarkdown
{
    public function convert($markup)
    {
        $markup = preg_replace('/<p>/i', "\n", $markup);
        $markup = preg_replace('/<\/p>/i', "\n", $markup);
        $markup = preg_replace('/><(strong|b)>/i', '> **', $markup);
        $markup = preg_replace('/<\/(strong|b)></i', '** <', $markup);
        $markup = preg_replace('/<\/?(strong|b)>/i', '**', $markup);
        $markup = preg_replace('/><(em|i)>/i', '> _', $markup);
        $markup = preg_replace('/<\/(em|i)></i', '_ <', $markup);
        $markup = preg_replace('/<\/?(em|i)>/i', '_', $markup);
        $markup = preg_replace('/<\/?ul>/i', "\n", $markup);
        $markup = preg_replace('/<li>/i', '* ', $markup);
        $markup = preg_replace('/<\/li>/i', '', $markup);
        $markup = preg_replace('/<h1\s+id="([-\w]+)">(.*?)<\/h1>/i', '\n# $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h2\s+id="([-\w]+)">(.*?)<\/h2>/i', '\n## $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h3\s+id="([-\w]+)">(.*?)<\/h3>/i', '\n### $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h4\s+id="([-\w]+)">(.*?)<\/h4>/i', '\n#### $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h5\s+id="([-\w]+)">(.*?)<\/h5>/i', '\n##### $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h6\s+id="([-\w]+)">(.*?)<\/h6>/i', '\n###### $2 {#$1}\n', $markup);
        $markup = preg_replace('/<h1>/i', "\n# ", $markup);
        $markup = preg_replace('/<h2>/i', "\n## ", $markup);
        $markup = preg_replace('/<h3>/i', "\n### ", $markup);
        $markup = preg_replace('/<h4>/i', "\n#### ", $markup);
        $markup = preg_replace('/<h5>/i', "\n##### ", $markup);
        $markup = preg_replace('/<h6>/i', "\n###### ", $markup);
        $markup = preg_replace('/<\/h[123456]>/i', "\n", $markup);
        $markup = preg_replace('/<a href="([^"]+)">([^<]+)<\/a>/i', '[$2]($1)', $markup);
        $markup = preg_replace('/<a href="([^"]+)" title="([^"]+)">([^<]+)<\/a>/i', '[$3]($1 "$2")', $markup);
        $markup = preg_replace('/<hr\s?\/?>/', "\n- - -\n", $markup);
        $markup = preg_replace('/\s*<table([^>]*)>/i', "\n\n<div$1>", $markup);
        $markup = preg_replace('/\s*<colgroup([^>]*)>.*?<\/colgroup>\s*/i', '', $markup);
        $markup = preg_replace('/\s*<\/?thead([^>]*)>\s*/i', '', $markup);
        $markup = preg_replace('/\s*<\/?tbody([^>]*)>\s*/i', '', $markup);
        $markup = preg_replace('/\s*<tr>\s*<th>/i', "\n", $markup);
        $markup = preg_replace('/<\/th>\s*<th>/i', ' | ', $markup);
        $markup = preg_replace('/<\/th>\s*<\/tr>/i', '\n - | -\n', $markup);
        $markup = preg_replace('/\s*<tr>\s*<td>/i', "\n", $markup);
        $markup = preg_replace('/<\/td>\s*<td>/i', ' | ', $markup);
        $markup = preg_replace('/<\/td>\s*<\/tr>/i', '', $markup);
        $markup = preg_replace('/\s*<\/table>/i', '\n<\/div>', $markup);
        $markup = preg_replace('/<\/?font[^>]*>/i', '', $markup);
        $markup = preg_replace('/<img(?:.*)src="(.*)"(?:.*)\/?>/i', "\n" . '![]($1)', $markup);

        return strip_tags($markup);
    }
}