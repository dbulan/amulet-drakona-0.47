<?php

$version = 0.22;


/*
���������� ����� ��� WAP �����.

�����������: 
- ����� ��������� ������ � ��������
- ���� � ��������� URL ���� admin= ������ $admin_login (��. ����), �� ����� ������� ��������� �������� ������ "�������", ������� ������ ��������� ��� ������ �� ����.
- ��� ��������� ��������� � ����� ����� forum.dat
- �������������� ������������� ������� ����
- ����� ������ ����������
- ������ �� ����� ��������� ��� ��������� GPRS

blade17@rambler.ru

0.21: �c������� ���, ����� ������ ���� �������� ������ ���������
*/

$admin_login="12345";								// ����� ������, ����������� ������� ���������


if ($PHP_SELF=='') $PHP_SELF = $_SERVER["PHP_SELF"];			// ���� � �������� �����, ��������: /phptest.php
$tmp=$QUERY_STRING;if($tmp=='') $tmp=$_SERVER["QUERY_STRING"];	// � ����������� �� �������� ������� �������
parse_str($tmp);									// ������ �������� ���������� $url, $p � �.�.
if ($admin_login && $admin==$admin_login) $admin="admin=$admin&amp;";	else $admin='';// ��� ������� ����� ����������� �� ��� ������
if ($admin) $admin2 = "?admin=".$admin_login; else $admin2='';
$tmp=str_replace("admin=".$admin_login,"",$tmp);

// � ����������� �� IP �����, ������� ��������� ����� �������� ���� � � ���� %��, �� ��� urldecode ������ �����������, ������� ������� ������� ���� ������� �� �������������� %�� � UTF-8
function tsdecode($s,$notr=0) {

	global $translit;
	if ($s!='') {
		if ($translit==1) {
			if($notr==0) {
				$s = urldecode($s);
				$c1eng = "ABCDEFGHIJKLMNOPRSTUVWXYZQabcdefghijklmnoprstuvwxyzq";
				$c1rus = "����������������������������������������������������";
				$search  = array ("'QQ'","'qq'","'CSH'","'SH'","'CH'","'YA'","'YU'","'YE'","'csh'","'sh'","'ch'","'ya'","'yu'","'ye'");
				$replace = array ("�","�","�","�","�","�","�","�","�","�","�","�","�","�");
				$s = preg_replace ($search, $replace, $s);
				for ($i=0;$i<strlen($c1eng);$i++) $s=str_replace($c1eng{$i},$c1rus{$i},$s);
				}
			} else {
				$s = str_replace("%D0%81","�",$s);
				$s = str_replace("%d0%81","�",$s);
				$s = str_replace("%D1%91","�",$s);
				$s = str_replace("%d1%91","�",$s);
				for ($i=144;$i<192;$i++) {$stmp = "%D0".urlencode(chr($i)); $s = str_replace(strtoupper($stmp),chr($i+48),$s); $s = str_replace(strtolower($stmp),chr($i+48),$s);}
				for ($i=128;$i<144;$i++) {$stmp = "%D1".urlencode(chr($i)); $s = str_replace(strtoupper($stmp),chr($i+112),$s);$s = str_replace(strtolower($stmp),chr($i+112),$s);}
				}
		$s = urldecode($s);
		}
	return $s;
	}
$from = tsdecode($from,1);
$subj = tsdecode($subj);
$msg = tsdecode($msg);


//===============================================
// ������� ������������� ���������
//===============================================

if ($table) {
	$text="<p>������� �������������:<br/>� - A<br/>� - B<br/>� - V<br/>� - G<br/>� - D<br/>� - E<br/>� - J<br/>� - Z<br/>� - I<br/>� - Y<br/>� - K<br/>� - L<br/>� - M<br/>� - N<br/>� - O<br/>� - P<br/>� - R<br/>� - S<br/>� - T<br/>� - U<br/>� - F<br/>� - H<br/>� - C<br/>� - CH<br/>� - SH<br/>� - CSH<br/>� - X<br/>� - Q<br/>� - QQ<br/>� - YE<br/>� - YU<br/>� - YA";
	tsecho($text);
	die("");
	} 

//===============================================
// ��������� ������ �� forum.dat
//===============================================

if (!file_exists("forum.dat")) {						// ���� ����� ���, �� ��������
	$file = fopen ("forum.dat", "w");
	fputs($file,"1\n");
	fclose ($file);
	}
$arr = array();
$file = fopen ("forum.dat", "r");
while(!feof($file)) {
	$stmp = tsdecode(trim(fgets($file,1024)),1);
	if ($stmp!='') $arr[] = $stmp;
	}
fclose ($file);
$count = count($arr);

//for ($j=0;$j<$count;$j++) echo $arr[$j]."<br>";

//echo print_r($arr);

//===============================================
// �������� ��������
//===============================================


$size=1200;

if ($tmp=='' || $start) {
	// ������� ���� ������ ������ 1200
	$text="<do type=\"options\" label=\"�����\"><go href=\"game.php\"/></do><p>
<anchor>[��������]
<go href=\"#add\"/>
</anchor>";
	if ($start=='') $start=$count-1; 
		else for ($i=0;$i<$count;$i++) {			// ���� ����� start � ������� �������
			$stmp=split("\|",$arr[$i]);
			if ($stmp[0]==$start) {$start=$i; break;}
			}
	$xCount=0;
	for ($i=$start;$i>0;$i--) {
		$sS="\n<br/>";
		$stmp=split("\|",$arr[$i]);
		for ($j=0;$j<$stmp[1];$j++) $sS.="&nbsp;&nbsp;";
		$sS.="<a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>";
		if (strlen(preg_replace("/[�-���]/e","11",$text.$sS))>$size) {$xCount=$stmp[0]; break;} else $text.=$sS;
		}

	if ($xCount>0) $text.="<br/><a href=\"".$PHP_SELF."?".$admin."start=".$xCount."\">[������...]</a>";
//	$text.="<br/><form action=$PHP_SELF method=get>��������:<br>���:<br><input name=from><br>����:<br><input name=subj><br>���������:<br><input name=msg><br><input type=hidden name=add value=1><input type=submit value=�������></form>";
	$text.="
</p>
</card>
<card id=\"add\" title=\"��������\">
<p>
���:<br/><input name=\"from\"/>
<br/>����:<br/><input name=\"subj\"/>
<br/>���������:<br/><input name=\"msg\"/>
<br/><select name=\"translit\" multiple=\"true\">
<option value=\"1\">��������</option>
</select>
<br/><a href=\"forum.php?".$admin."add=1&amp;from=$(from)&amp;subj=$(subj)&amp;msg=$(msg)&amp;translit=$(translit)\">���������</a>
<br/><a href=\"forum.php?".$admin."table=1\">������� �������������</a>";

	tsecho($text);
	die("");
	}

//===============================================
// ��������
//===============================================

if ($admin && $delete) {
	for ($i=0;$i<$count;$i++) {			// ���� ����� start � ������� �������
		$stmp=split("\|",$arr[$i]);
		if ($stmp[0]==$delete) {$delete=$i; break;}
		}

	$stmp=split("\|",$arr[$delete]);
	$indent = $stmp[1];
	array_splice($arr,$delete,1);

	// ���� � ������� ��� ������ �� �������
	$delete--;
	while ($delete>0) {
		$stmp=split("\|",$arr[$delete]);
		if ($stmp[1]>$indent) {
			array_splice($arr,$delete,1);
			$delete--;
			} else break;
		}

	$count=count($arr);
	// ��������� ��� �� ����...
	$file = fopen ("forum.dat", "w");
	for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,trim($arr[$i])."\n");
	fclose ($file);
	// ������� ����������
	//include("forum.php");
	$text="<do type=\"accept\" label=\"�����\"><go href=\"forum.php".$admin2;
	if ($start) $text.="&amp;start=$start";
	$text.="\"/></do><p>��������� �������.";
	tsecho($text);
	die("");
	}

//===============================================
// ����� ���������
//===============================================

if ($add) {
	// ��������� ����� ��������� � ������ ������
	if ($subj) {
		if ($from=='') $from="anonymous";
		$subj = strip_tags($subj);
		$from = strip_tags($from);
		$msg = strip_tags($msg);
		$stmp = "|0|$subj - $from|$msg";
		$all=implode("",$arr);
//		if (strpos($all,$stmp)===false) {
			$arr[]=$arr[0].$stmp;
			$arr[0]+=1;							// ����������� ����� ���������� ���������� ���������
			$count=count($arr);
			// ��������� ��� �� ����...
			$file = fopen ("forum.dat", "w");
			for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,urlencode(trim($arr[$i]))."\n");
			fclose ($file);
			// ������� ����������
			$text="<do type=\"accept\" label=\"�����\"><go href=\"forum.php".$admin2."\"/></do><p>��������� ���������.";
//			} else $text="<p>����� ��������� ��� ����, �������� � ��� ��������� GPRS ��� �� ������ ������ ���������";
		} else $text="<p>�� ������� ���� ���������.";
	tsecho($text);
	die("");
	}

//===============================================
// �����
//===============================================

if ($reply>0) {
	if ($subj) {
		if ($from=='') $from="anonymous";
		$subj = strip_tags($subj);
		$from = strip_tags($from);
		$msg = strip_tags($msg);

		// ��������� ����� � ����
		for ($i=0;$i<$count;$i++) {			// ���� ����� start � ������� �������
			$stmp=split("\|",$arr[$i]);
			if ($stmp[0]==$reply) {$reply=$i; break;}
			}
		$arr1 = array();
		$stmp=split("\|",$arr[$reply]);
		$arr1[]=$arr[0]."|".($stmp[1]+1)."|$subj - $from|$msg";
		$arr[0]+=1;						// ����������� ��������� �����
		$arr1[] = $arr[$reply];
		array_splice($arr,$reply,1,$arr1);
		$count=count($arr);
		// ��������� ��� �� ����...
		$file = fopen ("forum.dat", "w");
		for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,urlencode(trim($arr[$i]))."\n");
		fclose ($file);
		// ������� ����������
		$text="<do type=\"accept\" label=\"�����\"><go href=\"forum.php?".$admin."view=".$stmp[0]."\"/></do><p>��������� ���������.";		// ������� � ����, �� ���. ��������
		} else $text="<p>�� ������� ���� ���������.";
	tsecho($text);
	die("");
	}

//===============================================
// �������� ���������
//===============================================

if ($view>0) {
	for ($i=0;$i<$count;$i++) {			// ���� ����� start � ������� �������
		$stmp=split("\|",$arr[$i]);
		if ($stmp[0]==$view) {$view=$i; break;}
		}
	$text="<p><a href=\"forum.php".$admin2."\">[������]</a>";
	if ($admin) $text.="<a href=\"$PHP_SELF?".$admin."delete=".$stmp[0]."\">[�������]</a>";
	if ($arr[$view]!='') {
		$stmp=split("\|",$arr[$view]);
		$indent = $stmp[1];
		// ���� �� ��� �����...
		$i=$view+1;
		while ($i<$count) {
			$stmp=split("\|",$arr[$i]);
			if ($stmp[1]<$indent) {
				$text.="\n<br/>� ����� ��: <a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>"; 
				break;
				}
			$i++;
			}
		$stmp=split("\|",$arr[$view]);
		$text.="\n<br/>".$stmp[2]."<br/>".$stmp[3];
		$text.="
<br/><anchor>[��������]
<go href=\"#reply\"/>
</anchor>";
		// ���� ��� ������ �� �������
		$i=$view-1;
		$xCount=0;
		while ($i>0) {
			$sS="<br/>";
			$stmp=split("\|",$arr[$i]);
			if ($stmp[1]>$indent) {
				$sS="\n<br/>";
				for ($j=0;$j<$stmp[1];$j++) $sS.="&nbsp;&nbsp;";
				$sS.="<a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>";
				if (strlen(preg_replace("/[�-���]/e","11",$text.$sS))>$size) {$xCount=$stmp[0]; break;} else $text.=$sS;
				} else break;
			$i--;
			}

		} else $text="<p>��� ������ ���������: $view";

	if ($xCount>0) $text.="<br/><a href=\"".$PHP_SELF."?".$admin."start=".$xCount."\">[������...]</a>";
	$stmp=split("\|",$arr[$view]);
//	$text.="<br/><form action=$PHP_SELF method=get>��������:<br>���:<br><input name=from><br>����:<br><input name=subj><br>���������:<br><input name=msg><br><input type=hidden name=reply value=".$stmp[0]."><input type=submit value=�������></form>";
	$text.="
</p>
</card>
<card id=\"reply\" title=\"��������\">
<p>
���:<br/><input name=\"from\"/>
<br/>����:<br/><input name=\"subj\"/>
<br/>���������:<br/><input name=\"msg\"/>
<br/><select name=\"translit\" multiple=\"true\">
<option value=\"1\">��������</option>
</select>
<br/><a href=\"forum.php?".$admin."reply=".$stmp[0]."&amp;from=$(from)&amp;subj=$(subj)&amp;msg=$(msg)&amp;translit=$(translit)\">���������</a>
</p>
<p>
<br/><a href=\"forum.php?".$admin."table=1\">������� �������������</a>";
	tsecho($text);
	die("");
	}


function tsecho($s) {
header("Content-type:text/vnd.wap.wml;charset=utf-8"); 
echo "<?xml version=\"1.0\" ?>\n";?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml"> 
<?php setlocale (LC_CTYPE, 'ru_RU.CP1251'); 
function win2unicode ( $s ) { if ( (ord($s)>=192) & (ord($s)<=255) ) $hexvalue=dechex(ord($s)+848); if ($s=="�") $hexvalue="401"; if ($s=="�") $hexvalue="451"; return("&#x0".$hexvalue.";");} 
function translate($s) {return(preg_replace("/[�-���]/e","win2unicode('\\0')",$s));} 
	ob_start("translate");?>
<wml>
<head>
<meta forua="true" http-equiv="Cache-Control" content="must-revalidate"/>
<meta forua="true" http-equiv="Cache-Control" content="no-cache"/>
<meta forua="true" http-equiv="Cache-Control" content="no-store"/>
</head>
<card title="����� ����">
<? echo $s;?>

</p>
</card>
</wml>
<? ob_end_flush();
	die("");
	}

?>