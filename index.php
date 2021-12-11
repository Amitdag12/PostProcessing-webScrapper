<?php
$tag='<div id="myModal" class="modal">

<!-- Modal content -->
<div class="modal-content">
  <span class="close">&times;</span>
  <p>Some text in the Modal..</p>
</div>

</div>';
error_log($tag);
error_log(json_encode(HtmlToJson($tag)));
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
    $htmlString=remove_html_comments($htmlString);//removes comments
    $htmlString=GetTextAfter("<", $htmlString);//gets tag text
    $tag=GetTextBefore(">", $htmlString);//gets tag text
    //  $tag=HandleMissingSpace($tag);
    $tagAttributes=ExplodeTag($tag);//gets tag object attributes
    $tagContent ="";
    $htmlString=DeleteTextBetween("<", ">", $htmlString);//deletes tag
    $tagObject=array();
    $tagObject["tagName"]=$tagAttributes[0];
    //$tagAttributes=array_shift($tagAttributes);
    $tagObject["tagAtrributes"]=$tagAttributes;
    $tagObject["children"]=array();
    
    
    if (CheckIfAnyMore($htmlString)) {
        $htmlString="<".GetTextAfter("<", $htmlString);
        $result=HtmlToJson($htmlString);
        $htmlString=$result[0];
        $tagObject["children"][]=$result[1];
    } else {
    }
    $tagObject["content"]=GetTextBefore("<", $htmlString);
    return array($htmlString,$tagObject);
}
function GetTextBetween($start, $end, $text)
{
    $output=GetTextAfter($start, $text);
    $output=GetTextBefore($end, $output);
    return $output;
}
function DeleteTextBetween($start, $end, $text)
{
    $output=GetTextBefore($start, $text);
    $output.=GetTextAfter($end, $text);
    
    return $output;
}
function GetTextAfter($start, $text)
{
    return substr($text, strpos($text, $start)+strlen($start));
}
function GetTextBefore($end, $text)
{
    return substr($text, 0, strpos($text, $end));
}
function HandleMissingSpace($tag)
{
    return str_replace('  ', ' ', str_replace('"', '" ', $tag));
}
function ExplodeTag($tag)
{
    $tagAttributes=array();
    $text="";
    $attributeObject=array();
    $tagAttributes[]=GetTextBefore(' ', $tag);
    for ($i=strlen($tagAttributes[0]); $i <strlen($tag) ; $i++) {
        $currntChar=$tag[$i];
        if ($currntChar=='=') {
            $attributeObject["AtrributeName"]=trim($text);
            $i++;
            $text="";
            continue;
        }
        if ($currntChar=='"') {
            $attributeObject["AtrributeValue"]=trim($text);
            $tagAttributes[]=$attributeObject;
            $attributeObject=array();
            $text="";
        }
        $text.=$currntChar;
    }
    return $tagAttributes;
}
function CheckIfAnyMore($html)
{
    $index=strpos($html, "<");
    error_log("-------------".$index);
    return $html[$index+1]!="/"&&$index !== false;
}
function remove_html_comments($content = '')
{
    return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}
function FindFreeTag($html)
{
    $count=0;
    while (strpos($html, '<')!== false) {
        $indexStart=strpos($html, "<");
        $indexEnd=strpos($html, "</");
        $html[$indexStart]="A";
        $html[$indexEnd]="BB";
    }
    return strpos($html, "</");
}
