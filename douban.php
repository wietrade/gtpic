<?php
error_reporting(0);
header("Content-type: text/json;charset=utf-8");
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('FCPATH', str_replace("\\", "/", str_replace(SELF, '', __FILE__)));
if($_GET['id']){
$id=$_GET['id']; //豆瓣ID
$callback=isset($_GET['callback']) ? $_GET['callback'] : '';
if(stristr($id,".com")==true){
$id=str_substr('subject/','/', $id);
}else{
$id=$id;
}
$file = FCPATH.'/douban/'.$id.'.txt';
if(file_exists($file)){
$jsondata = file_get_contents($file);
}else{
$data = geturl('https://movie.douban.com/subject/'.$id.'/');
//$data=str_replace(" / ",",",$data); //别名
$txt3 = str_substr("首播:</span> ","<br/>", $data);
if($txt3==''){
$vod_pic = str_substr('"image": "','",', $data);
$vod_score=str_substr('<strong class="ll rating_num" property="v:average">','</strong>', $data); //评分
$vod_reurl=str_substr('data-url="','"', $data); //豆瓣地址
$a_name=str_substr('<i class="">','</i>', $data);
if(baohan($a_name,'剧情简介')=='1'){
$d_name=str_substr('<div>','的剧情简介', '<div>'.$a_name);
}elseif(baohan($a_name,'的分集短评')=='1'){
$d_name=str_substr('<div>','的分集', '<div>'.$a_name);
}elseif(baohan($a_name,'的演职员')=='1'){
$d_name=str_substr('<div>','的演职员', '<div>'.$a_name);
}elseif(baohan($a_name,'的图片')=='1'){
$d_name=str_substr('<div>','的图片', '<div>'.$a_name);
}elseif(baohan($a_name,'的短评')=='1'){
$d_name=str_substr('<div>','的短评', '<div>'.$a_name);
}elseif(baohan($a_name,'的影评')=='1'){
$d_name=str_substr('<div>','的影评', '<div>'.$a_name);
}
$a_name=str_substr('<span property="v:itemreviewed">','</span>', $data);
$a_name = str_replace("&#39;", "'", $a_name);
$vod_year=str_substr('<span class="year">(',')</span>', $data); //年代
$txt1 = str_substr("导演</span>: <span class='attrs'>","</span></span><br/>", $data);
$vod_director=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt1); //导演
$txt = str_substr("主演</span>: <span class='attrs'>","</span></span><br/>", $data);
$d_starring=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt);
$vod_actor=str_replace("'",',',$d_starring); //主演
$txt5 = str_substr("编剧</span>: <span class='attrs'>","</span></span><br/>", $data);
$vod_writer=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt5); //编辑
$txt2 = str_substr("类型:</span> ","<br/>", $data);
$vod_class=preg_replace("/<span[^>]*>(.*)<\/span>/isU",'${1}',$txt2); //分类
$vod_area = str_substr("制片国家/地区:</span> ","<br/>", $data); //地区
$vod_lang = str_substr("语言:</span> ","<br/>", $data); //语言
$vod_tv = str_substr("IMDb:</span> ","<br>", $data); //IMDb
$d_subname=str_substr("又名:</span> ","<br/>", $data);
$vod_sub=str_replace("'","’",$d_subname); //别名
$txt3 = str_substr("上映日期:</span> ","<br/>", $data);

$vod_pubdate=preg_replace("/<span[^>]*>(.*)<\/span>/isU",'${1}',$txt3); //上映日期


preg_match('/<span property="v:initialReleaseDate" content=[^>]*>(.*?)<\/span>/', $data, $matches);
$vod_pubdate = $matches[1]; //片长

preg_match('/<span property="v:runtime" content=[^>]*>(.*?)<\/span>/', $data, $matches);
$vod_duration = $matches[1]; //片长

preg_match('/<title>(.*?)<\/title>/s', $data, $matches);
$title = trim(str_replace([" (豆瓣)", "\n", "\r"], "",$matches[1])," ");
$vod_englist = trim(str_replace($title, "", $a_name)," "); //英文名

$pattern = '/<div id="related-pic" class="related-pic">(.*?)<\/div>/s';
if (preg_match($pattern, $data, $matches)) {
    $htmlcontent = $matches[1];
    $pattern = '/<img\s+[^>]*src=["\'](.*?)["\'][^>]*>/i';
    preg_match_all($pattern, $htmlcontent, $matches);
    $vod_picture = $matches[1]; //影片图
}

$pattern = '/<div id="recommendations" class="">(.*?)<\/div>/s'; // s 模式用于跨行匹配
if (preg_match($pattern, $data, $matches)) {
    $htmlcontent = $matches[1]; // 提取匹配到的内容
    preg_match_all('/<a\s+href=[\'"](.*?)[\'"].*?>/i', $htmlcontent, $matches);
    foreach ($matches[1] as $url) {
        $rel_ids[] = str_replace(['https://movie.douban.com/subject/','/?from=subject-page'], "", $url);
    }
    $rel_ids = implode(",", $rel_ids);
}


$d_content=str_substr('<span property="v:summary" class="">','</span>', $data);
if($d_content==''){
$d_content=str_substr('<span class="all hidden">','</span>', $data);
}
$vod_content=str_replace("'","’",$d_content); //简介
$vod_total=''; //集数
if(strstr($vod_area,"中国")==true){
    $vod_remarks='高清国语';
}elseif(strstr($vod_area,"台湾")==true){
    $vod_remarks='高清国语';
}else{
    $vod_remarks='高清中字';
}
}else{
$vod_pic = str_substr('"image": "','",', $data);
$vod_score=str_substr('<strong class="ll rating_num" property="v:average">','</strong>', $data); //评分
$vod_reurl=str_substr('data-url="','"', $data); //豆瓣地址
$a_name=str_substr('<i class="">','</i>', $data);
if(baohan($a_name,'剧情简介')=='1'){
$d_name=str_substr('<div>','的剧情简介', '<div>'.$a_name);
}elseif(baohan($a_name,'的分集短评')=='1'){
$d_name=str_substr('<div>','的分集', '<div>'.$a_name);
}elseif(baohan($a_name,'的演职员')=='1'){
$d_name=str_substr('<div>','的演职员', '<div>'.$a_name);
}elseif(baohan($a_name,'的图片')=='1'){
$d_name=str_substr('<div>','的图片', '<div>'.$a_name);
}elseif(baohan($a_name,'的短评')=='1'){
$d_name=str_substr('<div>','的短评', '<div>'.$a_name);
}elseif(baohan($a_name,'的影评')=='1'){
$d_name=str_substr('<div>','的影评', '<div>'.$a_name);
}
$a_name=str_substr('<span property="v:itemreviewed">','</span>', $data);
$a_name = str_replace("&#39;", "'", $a_name);
$vod_year=str_substr('<span class="year">(',')</span>', $data); //年代
$txt1 = str_substr("导演</span>: <span class='attrs'>","</span></span><br/>", $data);
$vod_director=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt1); //导演
$txt = str_substr("主演</span>: <span class='attrs'>","</span></span><br/>", $data);
$vod_actor=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt); //主演
$txt5 = str_substr("编剧</span>: <span class='attrs'>","</span></span><br/>", $data);
$vod_writer=preg_replace("/<a[^>]*>(.*)<\/a>/isU",'${1}',$txt5); //编辑
$txt2 = str_substr("类型:</span> ","<br/>", $data);
$vod_class=preg_replace("/<span[^>]*>(.*)<\/span>/isU",'${1}',$txt2); //分类
$vod_area = str_substr("制片国家/地区:</span> ","<br/>", $data); //国家
$vod_lang = str_substr("语言:</span> ","<br/>", $data); //语言
$vod_tv = str_substr("IMDb:</span> ","<br>", $data); //IMDb
$d_subname=str_substr("又名:</span> ","<br/>", $data);
$vod_sub=str_replace("'","’",$d_subname); //别名

$txt3 = str_substr("首播:</span> ","<br/>", $data);
$vod_pubdate=preg_replace("/<span[^>]*>(.*)<\/span>/isU",'${1}',$txt3); //上映日期
$vod_duration=str_substr('单集片长:</span> ','<br/>', $data); //片长
preg_match('/<title>(.*?)<\/title>/s', $data, $matches);
$title = trim(str_replace([" (豆瓣)", "\n", "\r"], "",$matches[1])," ");
$vod_englist = trim(str_replace($title, "", $a_name)," ");
$d_content=str_substr('<span property="v:summary" class="">','</span>', $data);
if($d_content==''){
$d_content=str_substr('<span class="all hidden">','</span>', $data);
}
$vod_content=str_replace("'","’",$d_content); //简介
$vod_total=str_substr('集数:</span> ','<br/>', $data); //集数
//preg_match("/(?<=\s|^)[A-Za-z\s\d.:'’·]+(?=$|\s)/u", html_entity_decode($a_name), $matches);
$vod_remarks='总集数'.$vod_total;
}
if($vod_sub==''){
$vod_sub=$a_name.$vod_year;
}
if($vod_year==''){
$vod_year='内详';
}
if($vod_score==''){
$vod_score=rand(1,4).'.'.rand(0,9);
}
if($vod_director==''){
$vod_director='内详';
}
if($vod_actor==''){
$vod_actor='内详';
}
if($vod_content==''){
$vod_content='内详';
}
if($d_name==''){
$vurl='{"code":102,"auth":"错误消息！","msg":"失败"}';
}else{
$info['vod_name'] = $d_name;
$info['vod_sub'] = str_replace(" / ", ",", $vod_sub);
$info['vod_behind'] = trim($vod_englist," ");
$info['vod_pic'] = $vod_pic;
$info['vod_year'] = $vod_year;
$info['vod_lang'] = str_replace(" / ", ",", $vod_lang);
$info['vod_area'] = str_replace(" / ", ",", $vod_area);
/*$info['vod_remarks'] = $vod_remarks; */
$info['vod_total'] = $vod_total;
$info['vod_tv'] = $vod_tv;
$info['vod_serial'] = '';
$info['vod_isend'] = 1;
$info['vod_class'] = str_replace(" / ", ",", $vod_class);
$info['vod_tag'] = '';
/*$info['vod_actor'] = str_replace(" / ", ",", $vod_actor);*/
$info['vod_actor'] = implode(",",array_slice(explode(",",str_replace(" / ", ",", $vod_actor)),0, 5)); 
$info['vod_director'] = $vod_director;
$info['vod_pubdate'] = str_replace(" / ", ",", $vod_pubdate);
$info['vod_writer'] = str_replace(" / ", ",", $vod_writer);
$info['vod_score'] = $vod_score;
$info['vod_score_num'] = rand(100,1000);
$info['vod_score_all'] = rand(200,500);
$info['vod_douban_score'] = $vod_score;
$info['vod_duration'] = strip_tags($vod_duration);
$info['vod_reurl'] = $vod_reurl;
$info['vod_author'] = $vod_author;
$info['vod_content'] = trim(cutstr_html($vod_content),"　");
$info['vod_douban_id'] = $id;
$info['vod_rel_vod'] = $rel_ids;
$info['vod_pic_screenshot'] = implode("$$$", $vod_picture);

$vurl='({"code":1,"auth":"API数据接口！","msg":"成功",

"data":'.json_encode($info,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).'});';
}
file_put_contents($file,$vurl);
}
echo $callback.$vurl;
/*
$json = ['code'=>1,'auth'=>'API数据接口！','msg'=>'成功','data'=>$info];
$jsondata = json_encode($json,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}
file_put_contents($file,$jsondata);
}
echo "(".$jsondata.");";

*/
}else{
echo $callback.'{"code":102,"auth":"错误！",
"msg":"错误"}';
}
function baohan($str,$needle){
$tmparray = explode($needle,$str);
if(count($tmparray)>1){
    $yyy='1';
}else{
    $yyy='2';
}
return $yyy;
}
function tugeturl($url){
     if(function_exists('curl_init')){
         $ch = curl_init();
         $timeout = 30;
         curl_setopt ($ch,CURLOPT_URL,$url);
         curl_setopt ($ch,CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
         curl_setopt ($ch,CURLOPT_SSL_VERIFYHOST, true);
         curl_setopt ($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt ($ch,CURLOPT_REFERER, $url);
         curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
         $handles = curl_exec($ch);
         curl_close($ch);
      }else{
         $handles = @file_get_contents($url);
      }
  return $handles;
}
function geturl($url){
     if(function_exists('curl_init')){
        $userAgent = 'Mozilla/5.0 (Linux; Android 5.0.2; Redmi Note 3 Build/LRX22G; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/45.0.2454.95 Mobile Safari/537.36  AliApp(DY/6.0.0) TBMovie/6.0.0 1080X1920';
        $referer = 'https://movie.douban.com/';
         $ch = curl_init();
         $timeout = 300;
         curl_setopt ($ch,CURLOPT_URL,$url);
         curl_setopt ($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt ($ch,CURLOPT_REFERER, $url);
         curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
         curl_setopt($ch, CURLOPT_REFERER, $referer);
         curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
         $handles = curl_exec($ch);
         curl_close($ch);
      }else{
         $handles = @file_get_contents($url);
      }
      $handles = @file_get_contents($url);
  return $handles;
}

/*
function geturl($url){
     if(function_exists('curl_init')){
         $ch = curl_init();
         $timeout = 30;
         curl_setopt ($ch,CURLOPT_URL,$url);
         curl_setopt ($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt ($ch,CURLOPT_REFERER, $url);  
         curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
         $handles = curl_exec($ch);
         curl_close($ch);
      }else{
         $handles = @file_get_contents($url);
      }
  return $handles;
}*/
function getvrl($url){
    $vurl = str_replace(array('"',"'"),array("'","‘"),$url);
    return $vurl;
}
function cutstr_html($string){
    // $string = strip_tags($string);
    $string = trim($string);
    $string = preg_replace('/(<a.*?>[\s\S]*?<\/a>)/','',$string);
    // $string = str_replace("\t","",$string);
    // $string = str_replace("\r\n","",$string);
    // $string = str_replace("\r","",$string);
    // $string = str_replace("\n","",$string);
    // $string = str_replace(" ","",$string);
    // $string = str_replace("　","",$string);
    return trim($string);
}
function str_substr($start, $end, $str){
    $temp = explode($start, $str, 2);
    $content = explode($end, $temp[1], 2);
    return $content[0];
}
?>
