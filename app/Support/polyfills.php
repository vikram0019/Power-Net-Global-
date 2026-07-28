<?php

if (! function_exists('mb_split')) {
    // Some PHP builds (notably a few cPanel EasyApache PHP packages) ship
    // mbstring without the mbregex component, so mb_split() is undefined.
    // Laravel's Str::studly()/headline()/initials()/title() call it on
    // every request via the session/cache/queue manager driver resolution,
    // so its absence is fatal without this. The only pattern Laravel ever
    // passes is '\s+', which behaves identically under PCRE.
    function mb_split(string $pattern, string $string, int $limit = -1): array|false
    {
        return preg_split('/'.$pattern.'/u', $string, $limit);
    }
}
