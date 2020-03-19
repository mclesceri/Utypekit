<?php

global $searchengines;
$searchengines = array(
    "Ask" => "ask jeeves",
    "Baidu" => "Baiduspider",
    "Bing" => "bingbot",
    "Bitly" => "bitlybot",
    "Cliqz" => "cliqzbot",
    "Duck Duck Go" => "duckduckbot",
    "Feedly" => "Feedly",
    "Facebook" => "facebookexternalhit",
    "Google" => "googlebot",
    "Google Ads" => "AdsBot-Google",
    "Google Feed" => "Feedfetcher-Google",
    "Google Page Speed Insight" => "Google Page Speed Insight",
    "Google Search Console" => "Google Search Console",
    "Google Site Verification" => "Google-Site-Verification",
    "Jetpack" => "Jetpack by WordPress.com",
    "MSN" => "msnbot",
    "TinEye" =>  "tineye-bot",
    "Twitter" => "twitterbot",
    "Yahoo!" => "yahoo! slurp",
    "Yandex" => "yandexbot"
);

function iqblockcountry_check_searchengine($user_agent,$allowse)
{
    global $searchengines;
    $issearchengine = FALSE;
    foreach ( $searchengines AS $se => $seua ) {
        if (is_array($allowse) && in_array($se,$allowse))
        {        
            if(stripos($user_agent, $seua) !== false) 
            {
                $issearchengine = TRUE;
            }
        }
    }
    return $issearchengine;
}

?>
