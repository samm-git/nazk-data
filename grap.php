<?php

/* quick & direty script to grab declarations from the website and conver them to the file tree */

// get list of the declations, api is broken so we are using "search" command for now

$src=httpPost("https://public.nazk.gov.ua/search","");

if ($src=='') {
    echo "Unable to grab page\n";
    exit(1);
}

preg_match_all('/href="\/declaration\/([a-z0-9-]+)">([^<]+)<\/a>/',$src,$matches);

$fp=fopen('decl/index.txt','w');
foreach($matches[1] as $key=>$v){
    fwrite($fp,$v." ".$matches[2][$key]."\n");
    if(!is_dir('decl/'.$v)) mkdir('decl/'.$v);
    file_put_contents("decl/".$v."/decl.json",
	file_get_contents('https://public-api.nazk.gov.ua/declarations-public/api/declaration/'.$v));
    file_put_contents("decl/".$v."/decl.html",
	file_get_contents('https://public.nazk.gov.ua/declaration/'.$v));

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