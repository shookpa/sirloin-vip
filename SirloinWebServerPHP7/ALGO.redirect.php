<?

# 4Admin(tm) URL Forwarding Script. (C) WEBMASTERS.COM. All Rights Reserved.

if ($_REQUEST['url']!="") {
   $url = strtolower($_REQUEST['url']);
   $url = ereg_replace("www\.", "", $url); }
else {
   $url = strtolower($_SERVER['HTTP_HOST']);
   $url = ereg_replace("www\.", "", $url); }

Function Forward($loc,$type) {
   global $url;
   if ($type=="Standard") header("Location: $loc");
   else {
        echo "<HTML>
<HEAD>
<TITLE>".$url."</TITLE>
<script language=\"javascript\"><!--begin hiding
function Clear() {
self.status=\"\";
setTimeout(\"Clear()\",10); }
Clear();
//end hiding --></script>
</HEAD>
<FRAMESET ROWS=\"*\" FRAMEBORDER=\"0\" BORDER=\"0\" FRAMESPACING=\"0\">
<frame src=\"".$loc."\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\" noresize>
<NOFRAMES>
<BODY>
<p>&nbsp;</p>
<p align=\"center\"><a href=\"".$loc."\"><b>Click Here To Enter</b></a></p>
</BODY>
</NOFRAMES>
</FRAMESET>
</HTML>"; }}

if ($url=="programadepuntos-vip.com") Forward("index.php","Frame");
elseif ($url=="newsletter.programadepuntos-vip.com") Forward("http://newsletters.programadepuntos-vip.com","Standard");
elseif ($url=="newsletters.programadepuntos-vip.com") Forward("http://programadepuntos-vip.ip-zone.com","Standard");
elseif ($url=="promociones.programadepuntos-vip.com") Forward("http://programadepuntos-vip.ip-zone.com","Standard");
elseif ($url=="promotions.programadepuntos-vip.com") Forward("http://programadepuntos-vip.ip-zone.com","Standard");
else Forward("index.php","Frame");

if ($url!="programadepuntos-vip.com") {
   $stats="/usr/local/4admin/apache/vhosts/programadepuntos-vip.com/logs/";
   if (is_dir($stats)) {
	$file=$stats.$url;
	$Stamp=date("d/m/Y:H:i:s");
	$Entry=$REMOTE_ADDR." ".$raw_url." - [".$Stamp."] \"GET / HTTP/1.0\" 200 537 \"".$HTTP_REFERER."\" \"".$HTTP_USER_AGENT."\"\n";
	if ($c=fopen($file, "a+")) {
	   fputs($c, $Entry);
	   fclose($c); }
	elseif ($c=fopen($file, "w")) {
	   flock($c,2);
	   fputs($c,$Entry);
	   flock($c,3);
	   fclose($c); }}}

?>
