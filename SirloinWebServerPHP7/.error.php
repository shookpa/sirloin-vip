<?
// Updated for New4Admin/PHP 7 06/25/18 s106
 
//Server specific file
include ("/usr/local/4admin/apache/htdocs/htdocs.inc.php");

error_reporting(E_ALL & ~E_NOTICE);
$url = strtolower($HTTP_HOST);
$Body="";

if($connect=fopen("http://".$Srv.".webmasters.com/404.htm", "r")) {
   while(!feof($connect)) {
        $Body.=fgets($connect, 255); }
   fclose($connect);
   $Body=preg_replace("%\[uri\]%", $REQUEST_URI, $Body);
   $Body=preg_replace("%\[url\]%", $url, $Body);
   echo $Body; }
else DIE ("<html><script language='JavaScript'>alert('ERROR 404 - File Not Found!')</script></html>");

?>
