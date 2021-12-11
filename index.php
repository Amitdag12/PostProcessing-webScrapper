<?php
function GetWebsiteHtml($url)
{
    require 'vendor/autoload.php';
    $httpClient = \Symfony\Component\Panther\Client::createChromeClient();
    // get response
    $response = $httpClient->get($url);
    //return site full html
    return $response->getCrawler()->html();
}
function HtmlToJson($htmlString)
{
}
function GetTextBetween($start, $end, $text)
{
}
function GetTextAfter($start, $text)
{
}
function GetTextBefore($end, $text)
{
}
