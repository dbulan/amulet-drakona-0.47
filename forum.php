<?php

$version = 0.22;


/*
Простейший форум для WAP сайта.

Достоинства: 
- можно добавлять записи и отвечать
- если в заголовке URL есть admin= паролю $admin_login (см. ниже), то около каждого сообщения появится ссылка "удалить", которая удалит сообщение все ответы на него.
- все сообщения храняться в одном файле forum.dat
- автоматическая перекодировка русских букв
- можно писать транслитом
- защита от копий сообщений при медленном GPRS

blade17@rambler.ru

0.21: иcправлен баг, когда нельзя было написать первое сообщение
*/

$admin_login="12345";								// логин админа, позволяющий удалять сообщения


if ($PHP_SELF=='') $PHP_SELF = $_SERVER["PHP_SELF"];			// путь к текущему файлу, например: /phptest.php
$tmp=$QUERY_STRING;if($tmp=='') $tmp=$_SERVER["QUERY_STRING"];	// в зависимости от настроек сервера сервера
parse_str($tmp);									// теперь появятся переменные $url, $p и т.д.
if ($admin_login && $admin==$admin_login) $admin="admin=$admin&amp;";	else $admin='';// эта строчка убдет добавляться во все ссылки
if ($admin) $admin2 = "?admin=".$admin_login; else $admin2='';
$tmp=str_replace("admin=".$admin_login,"",$tmp);

// в зависимости от IP гейта, телефон кириллицу может прислать хоть и в виде %ХХ, но при urldecode выйдет абракадабра, поэтому вручную заменим этим символы на соответсвующие %ХХ в UTF-8
function tsdecode($s,$notr=0) {

	global $translit;
	if ($s!='') {
		if ($translit==1) {
			if($notr==0) {
				$s = urldecode($s);
				$c1eng = "ABCDEFGHIJKLMNOPRSTUVWXYZQabcdefghijklmnoprstuvwxyzq";
				$c1rus = "АБЦДЕФГХИЖКЛМНОПРСТУВШЫЙЗЬабцдефгхижклмнопрстувшыйзь";
				$search  = array ("'QQ'","'qq'","'CSH'","'SH'","'CH'","'YA'","'YU'","'YE'","'csh'","'sh'","'ch'","'ya'","'yu'","'ye'");
				$replace = array ("Ъ","ъ","Щ","Ш","Ч","Я","Ю","Э","щ","ш","ч","я","ю","э");
				$s = preg_replace ($search, $replace, $s);
				for ($i=0;$i<strlen($c1eng);$i++) $s=str_replace($c1eng{$i},$c1rus{$i},$s);
				}
			} else {
				$s = str_replace("%D0%81","Ё",$s);
				$s = str_replace("%d0%81","Ё",$s);
				$s = str_replace("%D1%91","ё",$s);
				$s = str_replace("%d1%91","ё",$s);
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
// таблица перекодировки транслита
//===============================================

if ($table) {
	$text="<p>Таблица перекодировки:<br/>А - A<br/>Б - B<br/>В - V<br/>Г - G<br/>Д - D<br/>Е - E<br/>Ж - J<br/>З - Z<br/>И - I<br/>Й - Y<br/>К - K<br/>Л - L<br/>М - M<br/>Н - N<br/>О - O<br/>П - P<br/>Р - R<br/>С - S<br/>Т - T<br/>У - U<br/>Ф - F<br/>Х - H<br/>Ц - C<br/>Ч - CH<br/>Ш - SH<br/>Щ - CSH<br/>Ы - X<br/>Ь - Q<br/>Ъ - QQ<br/>Э - YE<br/>Ю - YU<br/>Я - YA";
	tsecho($text);
	die("");
	} 

//===============================================
// загружаем данные из forum.dat
//===============================================

if (!file_exists("forum.dat")) {						// если файла нет, то создадим
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
// основная страница
//===============================================


$size=1200;

if ($tmp=='' || $start) {
	// выводим пока размер меньше 1200
	$text="<do type=\"options\" label=\"Домой\"><go href=\"game.php\"/></do><p>
<anchor>[Написать]
<go href=\"#add\"/>
</anchor>";
	if ($start=='') $start=$count-1; 
		else for ($i=0;$i<$count;$i++) {			// ищем номер start в массиве записей
			$stmp=split("\|",$arr[$i]);
			if ($stmp[0]==$start) {$start=$i; break;}
			}
	$xCount=0;
	for ($i=$start;$i>0;$i--) {
		$sS="\n<br/>";
		$stmp=split("\|",$arr[$i]);
		for ($j=0;$j<$stmp[1];$j++) $sS.="&nbsp;&nbsp;";
		$sS.="<a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>";
		if (strlen(preg_replace("/[А-яЁё]/e","11",$text.$sS))>$size) {$xCount=$stmp[0]; break;} else $text.=$sS;
		}

	if ($xCount>0) $text.="<br/><a href=\"".$PHP_SELF."?".$admin."start=".$xCount."\">[Дальше...]</a>";
//	$text.="<br/><form action=$PHP_SELF method=get>Написать:<br>Имя:<br><input name=from><br>Тема:<br><input name=subj><br>Сообщение:<br><input name=msg><br><input type=hidden name=add value=1><input type=submit value=Послать></form>";
	$text.="
</p>
</card>
<card id=\"add\" title=\"Написать\">
<p>
Имя:<br/><input name=\"from\"/>
<br/>Тема:<br/><input name=\"subj\"/>
<br/>Сообщение:<br/><input name=\"msg\"/>
<br/><select name=\"translit\" multiple=\"true\">
<option value=\"1\">Транслит</option>
</select>
<br/><a href=\"forum.php?".$admin."add=1&amp;from=$(from)&amp;subj=$(subj)&amp;msg=$(msg)&amp;translit=$(translit)\">Отправить</a>
<br/><a href=\"forum.php?".$admin."table=1\">Таблица перекодировки</a>";

	tsecho($text);
	die("");
	}

//===============================================
// удаление
//===============================================

if ($admin && $delete) {
	for ($i=0;$i<$count;$i++) {			// ищем номер start в массиве записей
		$stmp=split("\|",$arr[$i]);
		if ($stmp[0]==$delete) {$delete=$i; break;}
		}

	$stmp=split("\|",$arr[$delete]);
	$indent = $stmp[1];
	array_splice($arr,$delete,1);

	// ищем и удаляем все ответы на текущее
	$delete--;
	while ($delete>0) {
		$stmp=split("\|",$arr[$delete]);
		if ($stmp[1]>$indent) {
			array_splice($arr,$delete,1);
			$delete--;
			} else break;
		}

	$count=count($arr);
	// сохраняем все на диск...
	$file = fopen ("forum.dat", "w");
	for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,trim($arr[$i])."\n");
	fclose ($file);
	// выводим обновление
	//include("forum.php");
	$text="<do type=\"accept\" label=\"Назад\"><go href=\"forum.php".$admin2;
	if ($start) $text.="&amp;start=$start";
	$text.="\"/></do><p>Сообщение удалено.";
	tsecho($text);
	die("");
	}

//===============================================
// новое сообщение
//===============================================

if ($add) {
	// добавляем новое сообщение в корень форума
	if ($subj) {
		if ($from=='') $from="anonymous";
		$subj = strip_tags($subj);
		$from = strip_tags($from);
		$msg = strip_tags($msg);
		$stmp = "|0|$subj - $from|$msg";
		$all=implode("",$arr);
//		if (strpos($all,$stmp)===false) {
			$arr[]=$arr[0].$stmp;
			$arr[0]+=1;							// увеличиваем номер последнего свободного сообщения
			$count=count($arr);
			// сохраняем все на диск...
			$file = fopen ("forum.dat", "w");
			for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,urlencode(trim($arr[$i]))."\n");
			fclose ($file);
			// выводим обновление
			$text="<do type=\"accept\" label=\"Назад\"><go href=\"forum.php".$admin2."\"/></do><p>Сообщение добавлено.";
//			} else $text="<p>Такое сообщение уже есть, возможно у вас медленный GPRS или вы дважды нажали Отправить";
		} else $text="<p>Не указана тема сообщения.";
	tsecho($text);
	die("");
	}

//===============================================
// ответ
//===============================================

if ($reply>0) {
	if ($subj) {
		if ($from=='') $from="anonymous";
		$subj = strip_tags($subj);
		$from = strip_tags($from);
		$msg = strip_tags($msg);

		// добавляем новое в базу
		for ($i=0;$i<$count;$i++) {			// ищем номер start в массиве записей
			$stmp=split("\|",$arr[$i]);
			if ($stmp[0]==$reply) {$reply=$i; break;}
			}
		$arr1 = array();
		$stmp=split("\|",$arr[$reply]);
		$arr1[]=$arr[0]."|".($stmp[1]+1)."|$subj - $from|$msg";
		$arr[0]+=1;						// увеличиваем свободный номер
		$arr1[] = $arr[$reply];
		array_splice($arr,$reply,1,$arr1);
		$count=count($arr);
		// сохраняем все на диск...
		$file = fopen ("forum.dat", "w");
		for ($i=0;$i<$count;$i++) if ($arr[$i]) fputs($file,urlencode(trim($arr[$i]))."\n");
		fclose ($file);
		// выводим обновление
		$text="<do type=\"accept\" label=\"Назад\"><go href=\"forum.php?".$admin."view=".$stmp[0]."\"/></do><p>Сообщение добавлено.";		// возврат к тому, на кот. отвечали
		} else $text="<p>Не указана тема сообщения.";
	tsecho($text);
	die("");
	}

//===============================================
// просмотр сообщения
//===============================================

if ($view>0) {
	for ($i=0;$i<$count;$i++) {			// ищем номер start в массиве записей
		$stmp=split("\|",$arr[$i]);
		if ($stmp[0]==$view) {$view=$i; break;}
		}
	$text="<p><a href=\"forum.php".$admin2."\">[Начало]</a>";
	if ($admin) $text.="<a href=\"$PHP_SELF?".$admin."delete=".$stmp[0]."\">[Удалить]</a>";
	if ($arr[$view]!='') {
		$stmp=split("\|",$arr[$view]);
		$indent = $stmp[1];
		// ищем на что ответ...
		$i=$view+1;
		while ($i<$count) {
			$stmp=split("\|",$arr[$i]);
			if ($stmp[1]<$indent) {
				$text.="\n<br/>В ответ на: <a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>"; 
				break;
				}
			$i++;
			}
		$stmp=split("\|",$arr[$view]);
		$text.="\n<br/>".$stmp[2]."<br/>".$stmp[3];
		$text.="
<br/><anchor>[Ответить]
<go href=\"#reply\"/>
</anchor>";
		// ищем все ответы на текущее
		$i=$view-1;
		$xCount=0;
		while ($i>0) {
			$sS="<br/>";
			$stmp=split("\|",$arr[$i]);
			if ($stmp[1]>$indent) {
				$sS="\n<br/>";
				for ($j=0;$j<$stmp[1];$j++) $sS.="&nbsp;&nbsp;";
				$sS.="<a href=\"".$PHP_SELF."?".$admin."view=".$stmp[0]."\">$stmp[2]</a>";
				if (strlen(preg_replace("/[А-яЁё]/e","11",$text.$sS))>$size) {$xCount=$stmp[0]; break;} else $text.=$sS;
				} else break;
			$i--;
			}

		} else $text="<p>Нет такого сообщения: $view";

	if ($xCount>0) $text.="<br/><a href=\"".$PHP_SELF."?".$admin."start=".$xCount."\">[Дальше...]</a>";
	$stmp=split("\|",$arr[$view]);
//	$text.="<br/><form action=$PHP_SELF method=get>Ответить:<br>Имя:<br><input name=from><br>Тема:<br><input name=subj><br>Сообщение:<br><input name=msg><br><input type=hidden name=reply value=".$stmp[0]."><input type=submit value=Послать></form>";
	$text.="
</p>
</card>
<card id=\"reply\" title=\"Ответить\">
<p>
Имя:<br/><input name=\"from\"/>
<br/>Тема:<br/><input name=\"subj\"/>
<br/>Сообщение:<br/><input name=\"msg\"/>
<br/><select name=\"translit\" multiple=\"true\">
<option value=\"1\">Транслит</option>
</select>
<br/><a href=\"forum.php?".$admin."reply=".$stmp[0]."&amp;from=$(from)&amp;subj=$(subj)&amp;msg=$(msg)&amp;translit=$(translit)\">Отправить</a>
</p>
<p>
<br/><a href=\"forum.php?".$admin."table=1\">Таблица перекодировки</a>";
	tsecho($text);
	die("");
	}


function tsecho($s) {
header("Content-type:text/vnd.wap.wml;charset=utf-8"); 
echo "<?xml version=\"1.0\" ?>\n";?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml"> 
<?php setlocale (LC_CTYPE, 'ru_RU.CP1251'); 
function win2unicode ( $s ) { if ( (ord($s)>=192) & (ord($s)<=255) ) $hexvalue=dechex(ord($s)+848); if ($s=="Ё") $hexvalue="401"; if ($s=="ё") $hexvalue="451"; return("&#x0".$hexvalue.";");} 
function translate($s) {return(preg_replace("/[А-яЁё]/e","win2unicode('\\0')",$s));} 
	ob_start("translate");?>
<wml>
<head>
<meta forua="true" http-equiv="Cache-Control" content="must-revalidate"/>
<meta forua="true" http-equiv="Cache-Control" content="no-cache"/>
<meta forua="true" http-equiv="Cache-Control" content="no-store"/>
</head>
<card title="Форум игры">
<? echo $s;?>

</p>
</card>
</wml>
<? ob_end_flush();
	die("");
	}

?>