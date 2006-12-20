<? 
# AST_timeonVDADallSUMMARY.php
# 
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# Summary for all campaigns live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# STOP=4000, SLOW=40, GO=4 seconds refresh interval
# 
# changes:
# 61102-1616 - first build
#

header ("Content-type: text/html; charset=utf-8");

require("dbconnect.php");

$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];
if (isset($_GET["RR"]))					{$RR=$_GET["RR"];}
	elseif (isset($_POST["RR"]))		{$RR=$_POST["RR"];}
if (isset($_GET["DB"]))					{$DB=$_GET["DB"];}
	elseif (isset($_POST["DB"]))		{$DB=$_POST["DB"];}
if (isset($_GET["adastats"]))			{$adastats=$_GET["adastats"];}
	elseif (isset($_POST["adastats"]))	{$adastats=$_POST["adastats"];}
if (isset($_GET["submit"]))				{$submit=$_GET["submit"];}
	elseif (isset($_POST["submit"]))	{$submit=$_POST["submit"];}
if (isset($_GET["ΕΠΙΒΕΒΑΙΩΣΗ"]))				{$ΕΠΙΒΕΒΑΙΩΣΗ=$_GET["ΕΠΙΒΕΒΑΙΩΣΗ"];}
	elseif (isset($_POST["ΕΠΙΒΕΒΑΙΩΣΗ"]))	{$ΕΠΙΒΕΒΑΙΩΣΗ=$_POST["ΕΠΙΒΕΒΑΙΩΣΗ"];}

if (!isset($RR))			{$gRRroup=4;}


$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]","",$PHP_AUTH_PW);

	$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6;";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$auth=$row[0];

  if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"VICI-PROJECTS\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Ακυρο Ονομα Χρήστη/Κωδικός Πρόσβασης: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}

$NOW_TIME = date("Y-m-d H:i:s");
$STARTtime = date("U");

$stmt="select campaign_id from vicidial_campaigns where active='Y';";
$rslt=mysql_query($stmt, $link);
if (!isset($DB))   {$DB=0;}
if ($DB) {echo "$stmt\n";}
$groups_to_print = mysql_num_rows($rslt);
$i=0;
while ($i < $groups_to_print)
	{
	$row=mysql_fetch_row($rslt);
	$groups[$i] =$row[0];
	$i++;
	}

if (!isset($RR))   {$RR=4;}

?>

<HTML>
<HEAD>
<STYLE type="text/css">
<!--
	.green {color: white; background-color: green}
	.red {color: white; background-color: red}
	.lightblue {color: black; background-color: #ADD8E6}
	.blue {color: white; background-color: blue}
	.midnightblue {color: white; background-color: #191970}
	.purple {color: white; background-color: purple}
	.violet {color: black; background-color: #EE82EE} 
	.thistle {color: black; background-color: #D8BFD8} 
	.olive {color: white; background-color: #808000}
	.yellow {color: black; background-color: yellow}
	.khaki {color: black; background-color: #F0E68C}
	.orange {color: black; background-color: orange}

	.r1 {color: black; background-color: #FFCCCC}
	.r2 {color: black; background-color: #FF9999}
	.r3 {color: black; background-color: #FF6666}
	.r4 {color: white; background-color: #FF0000}
	.b1 {color: black; background-color: #CCCCFF}
	.b2 {color: black; background-color: #9999FF}
	.b3 {color: black; background-color: #6666FF}
	.b4 {color: white; background-color: #0000FF}
-->
 </STYLE>

<? 

echo"<META HTTP-EQUIV=Refresh CONTENT=\"$RR; URL=$PHP_SELF?RR=$RR&DB=$DB&adastats=$adastats\">\n";
echo "<TITLE>VICIDIAL: Realtime All Εκστρατείες Summary</TITLE></HEAD><BODY BGCOLOR=WHITE>\n";
echo "<b>VICIDIAL: Realtime All Εκστρατείες Summary</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; \n";
echo "<a href=\"$PHP_SELF?group=$group&RR=4000&DB=$DB&adastats=$adastats\">STOP</a> | ";
echo "<a href=\"$PHP_SELF?group=$group&RR=40&DB=$DB&adastats=$adastats\">SLOW</a> | ";
echo "<a href=\"$PHP_SELF?group=$group&RR=4&DB=$DB&adastats=$adastats\">GO</a> ";
echo " &nbsp; &nbsp; &nbsp; </FONT>\n";
if ($adastats<2)
	{
	echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=2\"><font size=1>+ VIEW MORE SETTINGS</font></a>";
	}
else
	{
	echo "<a href=\"$PHP_SELF?group=$group&RR=$RR&DB=$DB&adastats=1\"><font size=1>- VIEW LESS SETTINGS</font></a>";
	}
echo " &nbsp; &nbsp; &nbsp; <a href=\"./server_stats.php\">ΑΝΑΦΟΡΕΣ</a>";
echo "<BR><BR>\n\n";

$k=0;
while($k<$groups_to_print)
{
$NFB = '<b><font size=3 face="courier">';
$NFE = '</font></b>';
$F=''; $FG=''; $B=''; $BG='';

$group = $groups[$k];
echo "<b><a href=\"./AST_timeonVDADall.php?group=$group&RR=$RR&DB=$DB&adastats=$adastats\">$group</a></b> &nbsp; - &nbsp; ";
echo "<a href=\"./admin.php?ADD=34&campaign_id=$group\">Modify</a>\n";

$stmt="select auto_dial_level,dial_status_a,dial_status_b,dial_status_c,dial_status_d,dial_status_e,lead_order,lead_filter_id,hopper_level,dial_method,adaptive_maximum_level,adaptive_dropped_percentage,adaptive_dl_diff_target,adaptive_intensity,available_only_ratio_tally,adaptive_latest_server_time,local_call_time,dial_timeout from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$DIALlev =		$row[0];
$DIALstatusA =	$row[1];
$DIALstatusB =	$row[2];
$DIALstatusC =	$row[3];
$DIALstatusD =	$row[4];
$DIALstatusE =	$row[5];
$DIALorder =	$row[6];
$DIALfilter =	$row[7];
$HOPlev =		$row[8];
$DIALmethod =	$row[9];
$maxDIALlev =	$row[10];
$DROPmax =		$row[11];
$targetDIFF =	$row[12];
$ADAintense =	$row[13];
$ADAavailonly =	$row[14];
$TAPERtime =	$row[15];
$CALLtime =		$row[16];
$DIALtimeout =	$row[17];

$stmt="select count(*) from vicidial_hopper where campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$VDhop = $row[0];

$stmt="select dialable_leads,calls_today,drops_today,drops_today_pct,differential_onemin,agents_average_onemin,balance_trunk_fill from vicidial_campaign_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$DAleads = $row[0];
$callsTODAY = $row[1];
$dropsTODAY = $row[2];
$drpctTODAY = $row[3];
$diffONEMIN = $row[4];
$agentsONEMIN = $row[5];
$balanceFILL = $row[6];
if ( ($diffONEMIN != 0) and ($agentsONEMIN > 0) )
	{
	$diffpctONEMIN = ( ($diffONEMIN / $agentsONEMIN) * 100);
	$diffpctONEMIN = sprintf("%01.2f", $diffpctONEMIN);
	}
else {$diffpctONEMIN = '0.00';}

$stmt="select sum(local_trunk_shortage) from vicidial_campaign_server_stats where campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$balanceSHORT = $row[0];

echo "<BR><table cellpadding=0 cellspacing=0><TR>";
echo "<TD ALIGN=RIGHT><font size=2><B>DIAL LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALlev&nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>TRUNK SHORT/FILL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $balanceSHORT / $balanceFILL &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>FILTER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALfilter &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B> TIME:</B> &nbsp; </TD><TD ALIGN=LEFT><font size=2> $NOW_TIME </TD>";
echo "";
echo "</TR>";

if ($adastats>1)
	{
	echo "<TR BGCOLOR=\"#CCCCCC\">";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp; <B>MAX LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $maxDIALlev &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED MAX:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DROPmax% &nbsp; &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TARGET DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $targetDIFF &nbsp; &nbsp; </TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>INTENSITY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAintense &nbsp; &nbsp; </TD>";
	echo "</TR>";

	echo "<TR BGCOLOR=\"#CCCCCC\">";
	echo "<TD ALIGN=RIGHT><font size=2><B>DIAL TIMEOUT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALtimeout &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>TAPER TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $TAPERtime &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>LOCAL TIME:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $CALLtime &nbsp;</TD>";
	echo "<TD ALIGN=RIGHT><font size=2><B>AVAIL ONLY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $ADAavailonly &nbsp;</TD>";
	echo "</TR>";
	}

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2><B>DIALABLE LEADS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DAleads &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>CALLS TODAY:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $callsTODAY &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>AVG AGENTS:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $agentsONEMIN &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>DIAL METHOD:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALmethod &nbsp; &nbsp; </TD>";
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2><B>HOPPER LEVEL:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $HOPlev &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>CALLS DROPPED:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $dropsTODAY &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>DL DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffONEMIN &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>STATUSES:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALstatusA, $DIALstatusB, $DIALstatusC, $DIALstatusD, $DIALstatusE &nbsp; &nbsp; </TD>";
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=RIGHT><font size=2><B>LEADS IN HOPPER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $VDhop &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>DROPPED PERCENT:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $drpctTODAY% &nbsp; &nbsp;</TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>DIFF:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $diffpctONEMIN% &nbsp; &nbsp; </TD>";
echo "<TD ALIGN=RIGHT><font size=2><B>ORDER:</B></TD><TD ALIGN=LEFT><font size=2>&nbsp; $DIALorder &nbsp; &nbsp; </TD>";
echo "</TR>";

echo "<TR>";
echo "<TD ALIGN=LEFT COLSPAN=8>";

### Header finish





################################################################################
### START calculating calls/agents
################################################################################

################################################################################
###### OUTBOUND CALLS
################################################################################
if (eregi("CLOSER",$group))
	{
	$stmt="select closer_campaigns from vicidial_campaigns where campaign_id='" . mysql_real_escape_string($group) . "';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$closer_campaigns = preg_replace("/^ | -$/","",$row[0]);
	$closer_campaigns = preg_replace("/ /","','",$closer_campaigns);
	$closer_campaigns = "'$closer_campaigns'";

	$stmt="select status from vicidial_auto_calls where status NOT IN('XFER') and ( (call_type='IN' and campaign_id IN($closer_campaigns)) or (campaign_id='" . mysql_real_escape_string($group) . "' and call_type='OUT') );";
	}
else
	{
	if ($group=='XXXX-ALL-ACTIVE-XXXX') {$groupSQL = '';}
	else {$groupSQL = " and campaign_id='" . mysql_real_escape_string($group) . "'";}

	$stmt="select status from vicidial_auto_calls where status NOT IN('XFER') $groupSQL;";
	}
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$parked_to_print = mysql_num_rows($rslt);
	if ($parked_to_print > 0)
	{
	$i=0;
	$out_total=0;
	$out_ring=0;
	$out_live=0;
	while ($i < $parked_to_print)
		{
		$row=mysql_fetch_row($rslt);

		if (eregi("LIVE",$row[0])) 
			{$out_live++;}
		else
			{
			if (eregi("CLOSER",$row[0])) 
				{$nothing=1;}
			else 
				{$out_ring++;}
			}
		$out_total++;
		$i++;
		}

		if ($out_live > 0) {$F='<FONT class="r1">'; $FG='</FONT>';}
		if ($out_live > 4) {$F='<FONT class="r2">'; $FG='</FONT>';}
		if ($out_live > 9) {$F='<FONT class="r3">'; $FG='</FONT>';}
		if ($out_live > 14) {$F='<FONT class="r4">'; $FG='</FONT>';}

		if (eregi("CLOSER",$group))
			{echo "$NFB$out_total$NFE current active calls&nbsp; &nbsp; &nbsp; \n";}
		else
			{echo "$NFB$out_total$NFE calls being placed &nbsp; &nbsp; &nbsp; \n";}
		
		echo "$NFB$out_ring$NFE calls ringing &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$F &nbsp;$out_live $FG$NFE calls waiting for agents &nbsp; &nbsp; &nbsp; \n";
		}
	else
	{
	echo "ΔΕΝ ΥΠΑΡΧΟΥΝ ΕΝΕΡΓΕΣ ΚΛΗΣΕΙΣ ΣΕ ΑΝΑΜΟΝΗ \n";
	}


###################################################################################
###### TIME ON SYSTEM
###################################################################################

$agent_incall=0;
$agent_ready=0;
$agent_paused=0;
$agent_total=0;

$stmt="select extension,user,conf_exten,status,server_ip,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish),call_server_ip,campaign_id from vicidial_live_agents where campaign_id='" . mysql_real_escape_string($group) . "';";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$talking_to_print = mysql_num_rows($rslt);
	if ($talking_to_print > 0)
	{
	$i=0;
	$agentcount=0;
	while ($i < $talking_to_print)
		{
		$row=mysql_fetch_row($rslt);
			if (eregi("READY|PAUSED",$row[4]))
			{
			$row[5]=$row[6];
			}
		$Lstatus =			$row[3];
		$status =			sprintf("%-6s", $row[3]);
		if (!eregi("INCALL|QUEUE",$row[3]))
			{$call_time_S = ($STARTtime - $row[6]);}
		else
			{$call_time_S = ($STARTtime - $row[5]);}

		$call_time_M = ($call_time_S / 60);
		$call_time_M = round($call_time_M, 2);
		$call_time_M_int = intval("$call_time_M");
		$call_time_SEC = ($call_time_M - $call_time_M_int);
		$call_time_SEC = ($call_time_SEC * 60);
		$call_time_SEC = round($call_time_SEC, 0);
		if ($call_time_SEC < 10) {$call_time_SEC = "0$call_time_SEC";}
		$call_time_MS = "$call_time_M_int:$call_time_SEC";
		$call_time_MS =		sprintf("%7s", $call_time_MS);
		$G = '';		$EG = '';
		if (eregi("PAUSED",$row[3])) 
			{
			if ($call_time_M_int >= 30) 
				{$i++; continue;} 
			else
				{
				$agent_paused++;  $agent_total++;
				}
			}

		if ( (eregi("INCALL",$status)) or (eregi("QUEUE",$status)) ) {$agent_incall++;  $agent_total++;}
		if ( (eregi("READY",$status)) or (eregi("CLOSER",$status)) ) {$agent_ready++;  $agent_total++;}
		$agentcount++;


		$i++;
		}

		if ($agent_ready > 0) {$B='<FONT class="b1">'; $BG='</FONT>';}
		if ($agent_ready > 4) {$B='<FONT class="b2">'; $BG='</FONT>';}
		if ($agent_ready > 9) {$B='<FONT class="b3">'; $BG='</FONT>';}
		if ($agent_ready > 14) {$B='<FONT class="b4">'; $BG='</FONT>';}

		echo "\n<BR>\n";

		echo "$NFB$agent_total$NFE agents logged in &nbsp; &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_incall$NFE agents in calls &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$B &nbsp;$agent_ready $BG$NFE agents waiting &nbsp; &nbsp; &nbsp; \n";
		echo "$NFB$agent_paused$NFE paused agents &nbsp; &nbsp; &nbsp; \n";
		
		echo "<PRE><FONT SIZE=2>";
		echo "";
	}
	else
	{
	echo " NO AGENTS ON CALLS<BR>\n";
	}

################################################################################
### END calculating calls/agents
################################################################################





echo "</TD>";
echo "</TR>";
echo "</TABLE>";

echo "</FORM>\n\n";
$k++;
}

?>
</PRE>

</BODY></HTML>