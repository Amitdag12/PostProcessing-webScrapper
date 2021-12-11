<?php
$tag='<div id="myModal" class="modal">
<div class="modal-content">
  <span class="close">&times;</span>
  <p>Some text in the Modal..</p>
</div>
</div>';
//error_log($tag);
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
    $htmlString="<".$htmlString;
    //  $tag=HandleMissingSpace($tag);
    $tagAttributes=ExplodeTag($tag);//gets tag object attributes
    $tagContent ="";
    $tagObject=array();
    $tagObject["tagName"]=$tagAttributes[0];
    //$tagAttributes=array_shift($tagAttributes);
    $tagObject["tagAtrributes"]=$tagAttributes;
    $tagObject["children"]=array();
    // error_log($htmlString);
    $htmlString=DeleteTextBetween("<", ">", $htmlString);//deletes tag
    if (!CheckIfAnyMore($htmlString)) {
        $tagObject["content"]=GetTextBefore("<", $htmlString);
    }
  

    $htmlString=substr($htmlString, 0, FindFreeTag($htmlString));
    
    // error_log($htmlString);
    //  return;
    while (CheckIfAnyMore($htmlString)) {
        $htmlString="<".GetTextAfter("<", $htmlString);
        error_log("1:".$htmlString);
        $result=HtmlToJson($htmlString);
        $tagObject["children"][]=$result;
        if (empty($result["children"])) {
            //  error_log($htmlString);
            error_log("2:".$result["tagAtrributes"][0]);
            //    $htmlString=substr($htmlString, strpos($htmlString, "</".$result["tagAtrributes"][0].">")+strlen($result["tagAtrributes"][0])+3);
            error_log("3:".$htmlString);
            error_log("4:".json_encode($result));
            if (trim($htmlString)=="") {
                error_log("im out of here");
                break;
            }
        }
       
        
        // $htmlString=GetTextAfter("<", $htmlString);
        //error_log($htmlString);
        // $htmlString=substr($htmlString, 0, FindFreeTag($htmlString));
        error_log("5:".$htmlString);
        $htmlString=substr($htmlString, strpos($htmlString, "</".$result["tagAtrributes"][0].">")+strlen($result["tagAtrributes"][0])+3);//remove used data
        error_log("6:".$htmlString);
    }
    if (!array_key_exists("content", $tagObject)) {
        $tagObject["content"]=GetTextBefore("<", $htmlString);
    }
    error_log("im out");
    return $tagObject;
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
    if (strpos($tag, " ")!==false) {
        $tagAttributes[]=GetTextBefore(' ', $tag);
    } else {
        $tagAttributes[]=$tag;
        return $tagAttributes;
    }
    

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
    if ($index === false) {
        return false;
    }
    // echo $index."    ".$html[$index]."   ".$html[$index+1]."        ".json_encode($html[$index+1]!="/"&&$index !== false)."</br> ";
    //  error_log("-------------".$index);
    return $html[$index+1]!="/";
}
function remove_html_comments($content = '')
{
    return preg_replace('/<!--(.|\s)*?-->/', '', $content);
}
function FindFreeTag($html)
{
    $count=0;
    while (CheckIfAnyMore($html)) {
        $indexStart=strpos($html, "<");
        $indexEnd=strpos($html, "</");
        $html[$indexStart]="A";
        $html[$indexEnd]="B";
        $html[$indexEnd+1]="B";
        //    error_log($html);
    }
    return strpos($html, "</");
}
