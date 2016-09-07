<?php

/* quick & direty script to grab declarations from the website and conver them to the file tree */

// get list of the declations, api is broken so we are using "search" command for now

$src=httpPost("https://public.nazk.gov.ua/search","");

if ($src=='') {
    echo "Unable to grab page\n";
    exit(1);
}

preg_match_all('/href="\/declaration\/([a-z0-9-]+)">([^<]+)<\/a>/',$src,$matches);

$fp=fopen('decl/index.md','w');
foreach($matches[1] as $key=>$v){
    fwrite($fp,"- [".trim($matches[2][$key])."](https://cdn.rawgit.com/samm-git/nazk-data/master/decl/".$v."/decl.html)\n");
    if(!is_dir('decl/'.$v)) mkdir('decl/'.$v);
    $content=file_get_contents('https://public-api.nazk.gov.ua/v1/declaration/'.$v);
    if($content)  file_put_contents("decl/".$v."/decl.json", $content);
    $content=file_get_contents('https://public.nazk.gov.ua/declaration/'.$v);
    if($content) file_put_contents("decl/".$v."/decl.html", $content);

}
fclose($fp);

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

?>