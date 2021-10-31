<?php

$version = 0.47;
if (file_exists("flag_debug")) $debug=1;
if (file_exists("flag_usesession")) $usesession=1;

$admin = "user.admin";								// ����� ������, ������ ������� ��� �����������
$gm_id = "";									// ���� game.php?gm=12345 - ��� ��� ������� ������� � ���� ����� Internet Exploorer ��� ������. ���� ������, �� ���������.

// ��������� �� ���������
$game_title = "������ �������";						// �������� ����
$game_file = "game.dat";							// ���� ��� ���������� ����
$time_logout = 5*60;								// ���� ������� ������ ������ �� ��������, ������� ��� �������
$time_objects_destroy = 10*60;						// ������� ��� ����������� ���������, ����� ���. ��� ������������
$time_crim = 10*60;								// �����, ������� ������� �������� ����������
$time_regenerate = 30;								// ����� ����������� ����� � ���� �� ������� (��� ����� ������� ����������� � ���������)
$points_limit_attr = 12;							// ����� ����� �� str.dex.int
$points_limit_attr_one=5;							// ������������ �������� str,dex,int
$points_limit_skills=40;							// ����� ����� �� ������
$points_limit_skills_one=5;							// ������. �������� ������ ������
$points_levelup = 5;								// �����., �� ������� ���������� ����� ����� ���������� � ������� ��� �������� �� ����. �������. ��� �������� ������� ���� ���������� � ����������� 1 ���� �����
$time_defspeed = 4;								// �� ��������� ������� ����� ������� �������� 4 �������
$count_show = 5;									// ����� ���-�� �������� ���������� �� ������ �� ���
$page_size = 1400;								// �� ����� �������� �������� ������ ���������� wml
$journal_count=5;									// ���-�� ������� � �������

// �������� ����������
$page_main = "";
$page_desc = "";

if ($PHP_SELF=='') $PHP_SELF = $_SERVER["PHP_SELF"];			// ���� � �������� �����
$tmp=$QUERY_STRING;if($tmp=='') $tmp=$_SERVER["QUERY_STRING"];	// � ����������� �� �������� �������
$tmp=urldecode($tmp);	
$tmp=preg_replace("/[^ -}�-�]/e","",$tmp);				// ������� ������������� �������
parse_str($tmp);									// ������ �������� ���������� $look, $attack � �.�.
if ($gm_id && $gm==$gm_id) $debug=1;

if (file_exists("flag_update") && (($gm_id &&$gm!=$gm_id) || !$gm_id))  eval(implode('',file("f_update.dat")));

$locations=array(
	"loc.0"=>		"Welcome|1|� ������|loc.lek1|� �������� ��������������|loc.drag1|�� ������ �� ��|loc.sklad1",
	"loc.lek1"=>	"���� ������|1|����� � ���|loc.lek|� ��������|loc.ak1|� �����|loc.bank1|�� ��|loc.0",
	"loc.lek"=>		"��� ������|1|����� �� �����|loc.lek1",
	"loc.drag1"=>	"����� ��������� ��������������|1|����� � ���|loc.drag|�� ������|loc.tenal|�� �����|loc.0",
	"loc.drag"=>	"������� ��������������|1|����� �� �����|loc.drag1",
	"loc.sklad1"=>	"������ � ������|1|� ������|loc.sklad2|� ����� �������|loc.uv|�� �����|loc.0",
	"loc.sklad2"=>	"����� ������|1|����� � �����|loc.sklad|����� � �������|loc.reg|�� ������|loc.jd3|�� �����|loc.sklad1",
	"loc.sklad"=>	"�� ������|0|����� �� �����|loc.sklad2",
	"loc.reg"=>		"������� ���������|1|����� �� �����|loc.sklad2",
	"loc.jd3"=>		"����� ����|1|� ������|loc.sklad2|�� ������|loc.jd4|�� �����|loc.jd1",
	"loc.jd4"=>		"�������|0|�� �����|loc.jd3|� ��������|loc.jd2",
	"loc.jd2"=>		"����� ��������|1|� ��������|loc.osobn|�� �����|loc.tenal|�� �����|loc.jd1|� ������� �� ���|loc.jd4",
	"loc.jd1"=>		"����� ����|1|� ��������|loc.jd2|� �������� �����|loc.tenal|�� ��|loc.jd3",
	"loc.osobn"=>	"�������|1|����� �� ��������|loc.jd2",
	"loc.tenal"=>	"�������� �����|1|��������� ������|loc.vv|� ��������|loc.drag1|� ����� �����|loc.jd1|� ����� ����� �� ������|loc.jd2",
	"loc.vv"=>		"��������� ������|1|����� �� ������|loc.zvv|�� �������|loc.vpl|� ��������|loc.ak1|� �������� �����|loc.tenal",
	"loc.vpl"=>		"��������� �������|1|� ��������� �������|loc.vv",
	"loc.ak1"=>		"����� ���������|1|����� � ��������|loc.ak|� �������|loc.kon1|� ��������� �������|loc.vv|� ���� ������|loc.lek1|� �����|loc.bank1",
	"loc.bank1"=>	"����� ������|1|����� � ����|loc.bank|� ��������|loc.ak1|� ������|loc.lek1|�� ��|loc.centr1|�� �����|loc.cpl",
	"loc.bank"=>	"� �����|1|����� �����|loc.bank1|�������� �����|loc.bank2",
	"loc.bank2"=>	"����� �����|1|����� � ����|loc.bank|� �������|loc.kon1|�� �������|loc.cpl|� �������� �������|loc.sv",
	"loc.kon1"=>	"����� �������|1|����� � �������|loc.kon|� ��������|loc.ak1|� �����|loc.bank2",
	"loc.kon"=>		"� �������|1|����� �� �����|loc.kon1",
	"loc.centr1"=>	"����������� ������|1|� �����|loc.bank1|����� � �������|loc.tav|������� � �������|loc.kuzn|����� � ������� ������|loc.br|�� ��|loc.centr2",
	"loc.tav"=>		"�������|1|����� �� �������|loc.centr1|������� � ������ ������|loc.tav1|� ������� ����|loc.tav2",
	"loc.tav1"=>	"�������|1|�� ������ ����|loc.tav3|� ������� ����|loc.tav2|� ������ �� �������|loc.tav",
	"loc.tav2"=>	"�������|1|�� ������ ����|loc.tav3|������� � ������ ������|loc.tav1|� ������ �� �������|loc.tav",
	"loc.tav3"=>	"�������|1|����� � ������ �����|loc.tav4|����� �� ������ �����|loc.tav5|����� �������� �� ������ ����|loc.tav1|�������� �������� �� ������ ����|loc.tav2",
	"loc.tav4"=>	"�������|1|����� � ��������|loc.tav3",
	"loc.tav5"=>	"�������|1|����� � ��������|loc.tav3",
	"loc.br"=>		"������� �����|1|����� �� �����|loc.centr1|������� � ����� �� ���|loc.or",
	"loc.or"=>		"������� ������|1|����� �� �����|loc.centr2|������� � ����� �� ������|loc.br",
	"loc.centr2"=>	"����������� �����|1|������� � �������|loc.kuzn|����� � ������� ������|loc.or|� ����� �������|loc.uv|�� �����|loc.centr1",
	"loc.kuzn"=>	"�������|1|����� � �������� �����|loc.br|����� � ����� �����|loc.or|�� �����|loc.centr1|�� ��|loc.centr2",
	"loc.uv"=>		"����� ������|1|�� �����|loc.centr2|�� ������|loc.sklad1|�� �����|loc.uz2|����� �� �����|loc.pristan",
	"loc.uz2"=>		"�������� �������|1|����� � ������� ��������|loc.prip|����� � ������� �� ���|loc.luk|�� ������ � ����� �������|loc.uv|�� �����|loc.uz1",
	"loc.prip"=>	"������� ��������|1|����� �� �����|loc.uz2",
	"loc.luk"=>		"������� ��� ��������|1|����� �� �����|loc.uz2",
	"loc.uz1"=>		"�������� �������|1|����� � ������� �� ������|loc.jiv|����� � ������� �� ���|loc.but|�� �����|loc.kaz1|�� ������|loc.uz2",
	"loc.jiv"=>		"������� ��������|1|����� �� �����|loc.uz1",
	"loc.but"=>		"������� ��������|1|����� �� �����|loc.uz1",
	"loc.kaz1"=>	"����� ���������|1|����� � �������|loc.kaz|� ��������� ����|loc.br3|�� �����|loc.dv1|�� ������|loc.uz1",
	"loc.kaz"=>		"�������|1|����� �� �����|loc.kaz1",
	"loc.dv1"=>		"����� ������� ����|1|����� � ������ ���|loc.dv|� ��������� ����|loc.br3|�� ������|loc.kaz1",
	"loc.dv"=>		"������ ���|0|����� �� �����|loc.dv1|����� � ����|loc.dv2",
	"loc.dv2"=>		"������ ���|0|����� �� �������|loc.dv",
	"loc.br3"=>		"��������� ����|0|� ������� ����|loc.dv1|� ��������|loc.kaz1|�� �����|loc.br4|�� �����|loc.br1",
	"loc.br4"=>		"��������� ����|0|�� ������|loc.br3|�� �����|loc.br2",
	"loc.br2"=>		"��������� ����|0|�� ������|loc.br1|�� ��|loc.br4",
	"loc.br1"=>		"��������� ����|1|� �������� �������|loc.sv|�������� �� �����|loc.br2|�������� �� ��|loc.br3",
	"loc.sv"=>		"�������� ������|1|����� �� ������|loc.zsv|����� � ������|loc.snar|� �����|loc.bank2|� ��������� ����|loc.br1",
	"loc.snar"=>	"������� ����������|1|����� �� ������|loc.sv",
	"loc.zvv"=>		"�� ���������� ��������|0|����� � �����|loc.vv|������ �� �����|loc.vd.1|��� �� �������|loc.vl.18",
	"loc.zsv"=>		"�� ��������� ��������|0|����� � �����|loc.sv|������ �� �����|loc.sd.1|�������� �� �����|loc.zb.1",
	"loc.zb.1"=>	"�������� �����|0|� �������� �������|loc.zsv|��� �� �����|loc.zl.3|����� �� ��|loc.zb.2",
	"loc.zb.2"=>	"�������� �����|0|����� �� �����|loc.zb.1|����� �� ��|loc.zb.3",
	"loc.zb.3"=>	"�������� �����|0|����� �� ������|loc.zb.2|����� �� ��|loc.zb.4",
	"loc.zb.4"=>	"�������� �����|0|����� �� �����|loc.zb.3|����� �� ������|loc.zb.5",
	"loc.zb.5"=>	"�������� �����|0|����� �� �����|loc.zb.4|�������� �� ������|loc.pristan",
	"loc.pristan"=>	"��������|1|����� � �����|loc.uv|����� �� �����|loc.zb.5|� ����|loc.port1",
	"loc.port1"=>	"����|1|�� ��������|loc.pristan|�� ������|loc.port2",
	"loc.port2"=>	"����|0|� ��� �� �������|loc.bl.1|�� �����|loc.port1",
	"loc.ak"=>		"��������|1|����� �� �����|loc.ak1|����� � �������|loc.ak4|� ����������|loc.ak2|� ��� ������������|loc.ak5|�� ������ ����|loc.ak3",
	"loc.ak4"=>		"��������� �������|1|����� � �������� ���|loc.ak",
	"loc.ak2"=>		"����������|1|����� � �������� ���|loc.ak",
	"loc.ak5"=>		"��� ������������|1|����� � �������� ���|loc.ak",
	"loc.ak3"=>		"��������|1|���������� �� ������ ����|loc.ak",
	"loc.cpl"=>		"����������� �������|1|� ����� �� �����|loc.bank2|� ����� �� ������|loc.bank1|�� ���� �������|loc.dvr",
	"loc.dvr"=>		"���� �������|1|����� �� �������|loc.cpl|����� � ������� ������|loc.dvr4|����� � ������ �� ������|loc.dvr2|� ���������|loc.dvr1",
	"loc.dvr2"=>	"���� �������|1|����� �� ����|loc.dvr",
	"loc.dvr4"=>	"���� �������|1|����� �� ����|loc.dvr",
	"loc.dvr1"=>	"���������|1|����� �� ����|loc.dvr|������� � ��������|loc.dvr5|������� � �������|loc.dvr3",
	"loc.dvr5"=>	"���������|1|�� ����� � ���������|loc.dvr1|������� � �������|loc.dvr3",
	"loc.dvr3"=>	"���������|1|�� ����� � ���������|loc.dvr1|������� � ��������|loc.dvr5",
	"loc.bl.1"=>	"�������� ���|0|�����|loc.bl.3|������|loc.bl.2|�� ����� � ����|loc.port2",
	"loc.bl.2"=>	"�������� ���|0|�����|loc.bl.4|������|loc.vl.1|�����|loc.bl.1",
	"loc.bl.3"=>	"�������� ���|0|�����|loc.bl.5|������|loc.bl.4|��|loc.bl.1",
	"loc.bl.4"=>	"�������� ���|0|�����|loc.bl.6|������|loc.vl.4|��|loc.bl.2|�����|loc.bl.3",
	"loc.bl.5"=>	"�������� ���|0|�����|loc.bl.7|������|loc.bl.6|��|loc.bl.3",
	"loc.bl.6"=>	"�������� ���|0|�����|loc.bl.8|������� ����� ������|loc.vl.7|��|loc.bl.4|�� �����|loc.bl.5",
	"loc.bl.7"=>	"�������� ���|0|�����|loc.vl.13|������|loc.bl.8|��|loc.bl.5",
	"loc.bl.8"=>	"�������� ���|0|�����|loc.vl.14|������� ����� �� ������|loc.vl.10|��|loc.bl.6|�����|loc.bl.7",
	"loc.kl.1"=>	"��������|0|����� �� ������|loc.vd.2|����� � �������|loc.kl.8|������|loc.kl.2|�����|loc.kl.15",
	"loc.kl.2"=>	"��������|0|�����|loc.kl.7|����� ������ �� ������|loc.kl.3|� ������� �� �����|loc.kl.1",
	"loc.kl.3"=>	"��������|0|����� � �����������|loc.kl.4|����� ������ �� �����|loc.kl.2|�����|loc.kl.6",
	"loc.kl.4"=>	"��������|0|�����|loc.kl.3",
	"loc.kl.5"=>	"��������|0|�����|loc.kl.6",
	"loc.kl.6"=>	"��������|0|�����|loc.kl.24|������|loc.kl.5|��|loc.kl.3|�����|loc.kl.7",
	"loc.kl.7"=>	"��������|0|�����|loc.kl.23|������|loc.kl.6|��|loc.kl.2",
	"loc.kl.8"=>	"��������|0|����� � ������|loc.kl.20|����� � ������� �� ���|loc.kl.1",
	"loc.kl.9"=>	"��������|0|�����|loc.kl.19|��|loc.kl.15|�����|loc.kl.10",
	"loc.kl.10"=>	"��������|0|�����|loc.kl.18|������|loc.kl.9|��|loc.kl.14",
	"loc.kl.11"=>	"��������|0|����� ������ �� �����|loc.kl.16|����� ������ �� ��|loc.kl.12",
	"loc.kl.12"=>	"��������|0|����� ������ �� �����|loc.kl.11|����� ������ �� ������|loc.kl.13",
	"loc.kl.13"=>	"��������|0|����� ������ ������|loc.kl.14|����� ������ �����|loc.kl.12",
	"loc.kl.14"=>	"��������|0|�����|loc.kl.10|������|loc.kl.15|�����|loc.kl.13",
	"loc.kl.15"=>	"��������|0|�����|loc.kl.9|������|loc.kl.1|�����|loc.kl.14",
	"loc.kl.16"=>	"��������|0|�����|loc.kl.33|������|loc.kl.17|��|loc.kl.11",
	"loc.kl.17"=>	"��������|0|�����|loc.kl.32|������|loc.kl.18|�����|loc.kl.16",
	"loc.kl.18"=>	"��������|0|�����|loc.kl.31|������|loc.kl.19|��|loc.kl.10|�����|loc.kl.17",
	"loc.kl.19"=>	"��������|0|�����|loc.kl.30|��|loc.kl.9|�����|loc.kl.18",
	"loc.kl.20"=>	"��������|0|����� � �������� �����|loc.kl.22|����� � ��������� �����|loc.kl.21|����� �� �����|loc.kl.8",
	"loc.kl.21"=>	"��������|0|�����|loc.kl.20",
	"loc.kl.22"=>	"��������|0|�����|loc.kl.20",
	"loc.kl.23"=>	"��������|0|�����|loc.kl.28|������|loc.kl.24|��|loc.kl.7",
	"loc.kl.24"=>	"��������|0|����� � �����������|loc.kl.25|�����|loc.kl.27|��|loc.kl.6|�����|loc.kl.23",
	"loc.kl.25"=>	"��������|0|����� �� �����|loc.kl.24",
	"loc.kl.26"=>	"��������|0|�����|loc.kl.27",
	"loc.kl.27"=>	"��������|0|�����|loc.kl.42|������|loc.kl.26|��|loc.kl.24|�����|loc.kl.28",
	"loc.kl.28"=>	"��������|0|����� � �����������|loc.kl.40|������|loc.kl.27|��|loc.kl.23|�����|loc.kl.29",
	"loc.kl.29"=>	"��������|0|�����|loc.kl.39|������|loc.kl.28|�����|loc.kl.30",
	"loc.kl.30"=>	"��������|0|����� � ��������|loc.kl.37|������|loc.kl.29|��|loc.kl.19|�����|loc.kl.31",
	"loc.kl.31"=>	"��������|0|�����|loc.kl.36|������|loc.kl.30|��|loc.kl.18|�����|loc.kl.32",
	"loc.kl.32"=>	"��������|0|�����|loc.kl.35|������|loc.kl.31|��|loc.kl.17|�����|loc.kl.33",
	"loc.kl.33"=>	"��������|0|�����|loc.kl.34|������|loc.kl.32|��|loc.kl.16",
	"loc.kl.34"=>	"��������|0|����� ������ �� ������|loc.kl.35|����� ������ �� ��|loc.kl.33",
	"loc.kl.35"=>	"��������|0|������|loc.kl.36|��|loc.kl.32|�����|loc.kl.34",
	"loc.kl.36"=>	"��������|0|��|loc.kl.31|�����|loc.kl.35",
	"loc.kl.37"=>	"��������|0|����� � �����|loc.kl.38|����� �� �����|loc.kl.30",
	"loc.kl.38"=>	"��������|0|�����|loc.kl.37",
	"loc.kl.39"=>	"��������|0|��|loc.kl.29",
	"loc.kl.40"=>	"��������|0|����� � �����|loc.kl.41|����� �� �����|loc.kl.28",
	"loc.kl.41"=>	"��������|0|�����|loc.kl.40",
	"loc.kl.42"=>	"��������|0|����� � �����|loc.kl.43|��|loc.kl.27",
	"loc.kl.43"=>	"��������|0|����� �� �����|loc.kl.42",
	"loc.sd.1"=>	"�������� ������|0|������ �� �����|loc.sd.2|� �������� �������|loc.zsv|� ����� �� ������|loc.sl.1|��� �� ������|loc.zl.1",
	"loc.sd.2"=>	"�������� ������|0|����� � ���|loc.kzd|������ �� �����|loc.sd.3|������ �� ��|loc.sd.1|�� �����|loc.zl.10",
	"loc.sd.3"=>	"�������� ������|0|������ �� �����|loc.sd.4|��� �� �������|loc.sl.9|������ �� ��|loc.sd.2|��� �� ������|loc.zl.11",
	"loc.sd.4"=>	"�������� ������|0|������ �� ��|loc.sd.3|��� �� ������|loc.zl.12",
	"loc.kzd"=>		"��� � ������|0|����� �� �����|loc.sd.2",
	"loc.sl.1"=>	"�������� ���|0|������ �� ������|loc.sd.1|�������� ����� � ������|loc.sl.6|�������� ����� � ���|loc.sl.2",
	"loc.sl.2"=>	"�������� ���|0|����� ������ �� ������|loc.sl.3|��� �� ������|loc.sl.1",
	"loc.sl.3"=>	"�������� ���|0|����� ������ �� �����|loc.sl.2|�� ������|loc.sl.4",
	"loc.sl.4"=>	"�������� ���|0|�� ������ � �������|loc.vd.1|�� ������-������|loc.vd.2|�������� ����� � ������|loc.sl.5|�������� ����� � ���|loc.sl.3",
	"loc.sl.5"=>	"�������� ���|0|�� ����� ����� ������|loc.sl.8|����� ������ �� �����|loc.sl.6|�� ���-������|loc.sl.4",
	"loc.sl.6"=>	"�������� ���|0|� ��� �� ������|loc.sl.7|����� ������ �� ������|loc.sl.5|�� ���-�����|loc.sl.1",
	"loc.sl.7"=>	"�������� ���|0|� �����|loc.sl.6|�����|loc.sl.10|������|loc.sl.8",
	"loc.sl.8"=>	"�������� ���|0|�� �� � �����|loc.sl.5|�����|loc.sl.11|�����|loc.sl.7",
	"loc.sl.9"=>	"�������� ���|0|������ �� ������|loc.sd.3|�� ������|loc.sl.10",
	"loc.sl.10"=>	"�������� ���|0|������|loc.sl.11|��|loc.sl.7|�����|loc.sl.9",
	"loc.sl.11"=>	"�������� ���|0|������|loc.sl.12|��|loc.sl.8|�����|loc.sl.10",
	"loc.sl.12"=>	"�������� ���|0|������|loc.sl.14|�����|loc.sl.11",
	"loc.sl.14"=>	"�������� ���|0|������|loc.sl.15|�����|loc.sl.12",
	"loc.sl.15"=>	"�������� ���|0|������ �� �������|loc.vd.7|����� �������� �� ��|loc.sl.16|����� �������� �� �����|loc.sl.14",
	"loc.sl.16"=>	"�������� ���|0|������ �� �������|loc.vd.6|�� �����|loc.sl.15|�� ��|loc.sl.17",
	"loc.sl.17"=>	"�������� ���|0|������ �� �������|loc.vd.5|������ �� ���|loc.vd.4|�� �����|loc.sl.16",
	"loc.vd.1"=>	"��������� ������|0|������ �� ������|loc.vd.2|��� �� �������|loc.vl.23|������ �� ���|loc.zvv|� ����� �� ������|loc.sl.4",
	"loc.vd.2"=>	"��������� ������|0|����� � �������|loc.kl.1|�� ������ �� ������|loc.vd.3|�� ������ �� ��|loc.vd.1|�� �����|loc.sl.4|� ��� �� ���|loc.vl.23",
	"loc.vd.3"=>	"��������� ������|0|������ �� ������|loc.vd.4|��� �� ���|loc.vl.24|������ �� �����|loc.vd.2",
	"loc.vd.4"=>	"��������� ������|0|�����|loc.sl.17|������ �� ������|loc.vd.5|��|loc.vl.25|������ �� �����|loc.vd.3",
	"loc.vd.5"=>	"��������� ������|0|������ �� �����|loc.vd.6|������|loc.vl.28|������ �� ��|loc.vd.4|�����|loc.sl.17",
	"loc.vd.6"=>	"��������� ������|0|������ �� �����|loc.vd.7|������|loc.vl.29|������ �� ��|loc.vd.5|�����|loc.sl.16",
	"loc.vd.7"=>	"��������� ������|0|������|loc.vl.30|������ �� ��|loc.vd.6|�����|loc.sl.15",
	"loc.vl.1"=>	"��������� ���|0|�����|loc.vl.4|������|loc.vl.2|�����|loc.bl.2",
	"loc.vl.2"=>	"��������� ���|0|�����|loc.vl.5|������|loc.vl.3|�����|loc.vl.1",
	"loc.vl.3"=>	"��������� ���|0|�����|loc.vl.6|�����|loc.vl.2",
	"loc.vl.4"=>	"��������� ���|0|�����|loc.vl.7|������|loc.vl.5|��|loc.vl.1|�����|loc.bl.4",
	"loc.vl.5"=>	"��������� ���|0|�����|loc.vl.8|������|loc.vl.6|��|loc.vl.2|�����|loc.vl.4",
	"loc.vl.6"=>	"��������� ���|0|�����|loc.vl.9|��|loc.vl.3|�����|loc.vl.5",
	"loc.vl.7"=>	"��������� ���|0|�����|loc.vl.10|������|loc.vl.8|��|loc.vl.4|������� ����� �� �����|loc.bl.6",
	"loc.vl.8"=>	"��������� ���|0|�����|loc.vl.11|������|loc.vl.9|��|loc.vl.5|�����|loc.vl.7",
	"loc.vl.9"=>	"��������� ���|0|�����|loc.vl.12|��|loc.vl.6|�����|loc.vl.8",
	"loc.vl.10"=>	"��������� ���|0|�����|loc.vl.15|������|loc.vl.11|��|loc.vl.7|������� ����� �� �����|loc.bl.8",
	"loc.vl.11"=>	"��������� ���|0|�����|loc.vl.16|������|loc.vl.12|��|loc.vl.8|�����|loc.vl.10",
	"loc.vl.12"=>	"��������� ���|0|�����|loc.vl.17|��|loc.vl.9|�����|loc.vl.11",
	"loc.vl.13"=>	"��������� ���|0|�����|loc.vl.18|������|loc.vl.14|��|loc.bl.7",
	"loc.vl.14"=>	"��������� ���|0|�����|loc.vl.19|������|loc.vl.15|��|loc.bl.8|�����|loc.vl.13",
	"loc.vl.15"=>	"��������� ���|0|�����|loc.vl.20|������|loc.vl.16|��|loc.vl.10|�����|loc.vl.14",
	"loc.vl.16"=>	"��������� ���|0|�����|loc.vl.21|������|loc.vl.17|��|loc.vl.11|�����|loc.vl.15",
	"loc.vl.17"=>	"��������� ���|0|�����|loc.vl.22|��|loc.vl.12|�����|loc.vl.16",
	"loc.vl.18"=>	"��������� ���|0|�����|loc.vl.23|������|loc.vl.19|��|loc.vl.13|�� ������|loc.zvv",
	"loc.vl.19"=>	"��������� ���|0|�����|loc.vl.24|������|loc.vl.20|��|loc.vl.14|�����|loc.vl.18",
	"loc.vl.20"=>	"��������� ���|0|�����|loc.vl.25|������|loc.vl.21|��|loc.vl.15|�����|loc.vl.19",
	"loc.vl.21"=>	"��������� ���|0|�����|loc.vl.26|������|loc.vl.22|��|loc.vl.16|�����|loc.vl.20",
	"loc.vl.22"=>	"��������� ���|0|�����|loc.vl.27|��|loc.vl.17|�����|loc.vl.21",
	"loc.vl.23"=>	"��������� ���|0|�����|loc.vd.2|������|loc.vl.24|��|loc.vl.18|�����|loc.vd.1",
	"loc.vl.24"=>	"��������� ���|0|�����|loc.vd.3|������|loc.vl.25|��|loc.vl.19|�����|loc.vl.23",
	"loc.vl.25"=>	"��������� ���|0|�����|loc.vd.4|������|loc.vl.26|��|loc.vl.20|�����|loc.vl.24",
	"loc.vl.26"=>	"��������� ���|0|�����|loc.vl.28|������|loc.vl.27|��|loc.vl.21|�����|loc.vl.25",
	"loc.vl.27"=>	"��������� ���|0|�����|loc.vl.28|��|loc.vl.22|�����|loc.vl.26",
	"loc.vl.28"=>	"��������� ���|0|�����|loc.vl.29|��|loc.vl.26|���-������|loc.vl.27|�����|loc.vd.5",
	"loc.vl.29"=>	"��������� ���|0|�����|loc.vl.30|��|loc.vl.28|�����|loc.vd.6",
	"loc.vl.30"=>	"��������� ���|0|��|loc.vl.29|�� ������|loc.vd.7",
	"loc.zl.1"=>	"�������� ���|0|� �������� �������|loc.sd.1|�����|loc.zl.10|�����|loc.zl.2",
	"loc.zl.2"=>	"�������� ���|0|�����|loc.zl.9|������|loc.zl.1|�����|loc.zl.3",
	"loc.zl.3"=>	"�������� ���|0|�� ����� �� ���|loc.zb.1|����� ���� �� �����|loc.zl.4|�����|loc.zl.8|������|loc.zl.2",
	"loc.zl.4"=>	"�������� ���|0|����� ���� �� ������|loc.zl.3|����� ���� �� �����|loc.zl.5|�����|loc.zl.7",
	"loc.zl.5"=>	"�������� ���|0|����� ���� �� ������|loc.zl.4|�� �����|loc.zl.6",
	"loc.zl.6"=>	"�������� ���|0|�����|loc.zl.15|������|loc.zl.7|��|loc.zl.5",
	"loc.zl.7"=>	"�������� ���|0|�����|loc.zl.14|�����|loc.zl.6|��|loc.zl.4|������|loc.zl.8",
	"loc.zl.8"=>	"�������� ���|0|�����|loc.zl.13|������|loc.zl.9|��|loc.zl.3|�����|loc.zl.7",
	"loc.zl.9"=>	"�������� ���|0|�����|loc.zl.12|������|loc.zl.10|��|loc.zl.2|�����|loc.zl.8",
	"loc.zl.10"=>	"�������� ���|0|������ �� �������|loc.sd.2|�����|loc.zl.11|��|loc.zl.1|�����|loc.zl.9",
	"loc.zl.11"=>	"�������� ���|0|������ �� �������|loc.sd.3|�� ������-�����|loc.zl.12|����� ������ �� ��|loc.zl.10",
	"loc.zl.12"=>	"�������� ���|0|������ �� �������|loc.sd.4|�� ���-������|loc.zl.11|��|loc.zl.9|�����|loc.zl.13",
	"loc.zl.13"=>	"�������� ���|0|����� � ���|loc.krestd|������|loc.zl.12|��|loc.zl.8|�����|loc.zl.14",
	"loc.zl.14"=>	"�������� ���|0|������|loc.zl.13|��|loc.zl.7|�����|loc.zl.15",
	"loc.zl.15"=>	"�������� ���|0|������|loc.zl.14|��|loc.zl.6",
	"loc.krestd"=>	"������������ ���|0|����� �� �����|loc.zl.13",
	);

// ���������� ����������� ������
error_reporting (ERROR | WARNING);
function myErrorHandler ($errno, $errstr, $errfile, $errline) {}
set_error_handler("myErrorHandler");

if (!$debug) {
	header("Content-type:text/vnd.wap.wml;charset=utf-8"); 
	echo "<?xml version=\"1.0\"?>\n";
	echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">"; 
	setlocale (LC_CTYPE, 'ru_RU.CP1251'); 
	function win2unicode ( $s ) { if ( (ord($s)>=192) & (ord($s)<=255) ) $hexvalue=dechex(ord($s)+848); if ($s=="�") $hexvalue="401"; if ($s=="�") $hexvalue="451"; return("&#x0".$hexvalue.";");} 
	function translate($s) {return(preg_replace("/[�-���]/e","win2unicode('\\0')",$s));} 
	}

if ($usesession) {
	ini_set('session.use_trans_sid','0');
	ini_set('url_rewriter.tags','');
	if ($sid) {session_id($sid);session_start(); $login = $_SESSION["login"];}	// ���� �������� ����� �������
	if (!$login && !$site) $tmp='';					// ��������� �������� ��������, ���� �� ���� �������� �����
	} else {
		if (!$login) $login=$sid; else {if (substr($login,0,5)!='user.') $login='user.'.$login; $sid=$login;}	// FIX: ��������
		$sid=$login."&p=$p";
		}
$login=str_replace('$','',$login);	// ����� PHP �� �������� �� ����������
if ($sid) $sid.="&r=".rand(1,99);	// ��� ����� ������ �������� ����������� ������, � �� ������� �� ����

if (file_exists($game_file)) {
	$file_save = fopen($game_file,"r+");
	if ($file_save===FALSE) {usleep(100000); $file_save = fopen($game_file,"r+");} // ���� �� 100 �����������, ���� ������ �� �������� �� �������� 
	flock($file_save,2);	// ���� �� ������� ���� ��� ���������� ��� ������ �� ����������
	$game="";
	while (!feof ($file_save)) $game.= fgets($file_save, filesize($game_file));
	$game = unserialize($game);
	} else {$file_save = fopen($game_file,"w+");eval(implode('',file("f_blank.dat")));}

// html ���������
if ($gm_id && $gm==$gm_id) {
	if ($load_offline==1) eval(implode('',file("f_loadoffline.dat")));
	$sid.="&login=user.admin&gm=".$gm_id;
	$login="user.admin";
	eval(implode('',file("f_adminfull.dat")));
	savegame();
	die("");
	}

if ($site || $tmp=='') eval(implode('',file("f_site.dat"))); // ��� ��� �������� �����
if (!$login || !isset($game["players"][$login])) {$site="connect";eval(implode('',file("f_site.dat")));}

if (!$usesession) {
	$info=split("\|",$game["loc"][$game["players"][$login]][$login]["info"]);
	if ($info[0]!=$p) msg("������������ ������<br/><a href=\"$PHP_SELF\">�� �������</a><br/>",$game_title,0,'none');
	}
$player=&$game["loc"][$game["players"][$login]][$login];
$player["time"]=time();

// ������������� ��������
ai();

// ������������ ������
if ($macros) eval(implode('',file("f_macros.dat")));	// $macros ����������� ������!
if ($adm) eval(implode('',file("f_admin.dat")));
if ($look) eval(implode('',file("f_look.dat")));
if ($speak) eval(implode('',file("f_speak.dat")));
if ($say) eval(implode('',file("f_say.dat")));
if ($msg) eval(implode('',file("f_msg.dat")));
if ($attack) eval(implode('',file("f_attack.dat")));
if ($take) eval(implode('',file("f_take.dat")));
if ($drop) eval(implode('',file("f_drop.dat")));
if ($use) eval(implode('',file("f_use.dat")));		// $use ����������� ������ $list!
if ($list) {
	if ($list=='skill') eval(implode('',file("f_listskill.dat")));
	if ($list=='magic') eval(implode('',file("f_listmagic.dat")));
	if ($list=='inv') eval(implode('',file("f_listinv.dat")));
	if ($list=='all') eval(implode('',file("f_listall.dat")));
	}
if ($go) eval(implode('',file("f_go.dat")));


// ���������� ����
//link����

// ����� ���������
$count=0;
foreach($player["msg"] as $i) if ($i) $count++;
if ($count) $page_main.= "<p><a href=\"$PHP_SELF?sid=$sid&msg=1\">���.�����.:</a> $count"; 

// MAIN PAGE
if ($count) $page_main.= "\n<br/>"; else $page_main.= "<p>";
$page_main.= $player["life"]."/".$player["life_max"]." (".$player["mana"]."/".$player["mana_max"].")";
if ($player["ghost"]) $page_main.= "<br/>�� �������";
if ($player["crim"]) $page_main.= "<br/>�� ����������";

// SOUNDS
$stmp="";
$loc=split("\|",$locations[$player["loc"]]);
for ($i=3;$i<count($loc);$i++) {
	if (substr($loc[$i],0,4)=='loc.') if (count($game["loc"][$loc[$i]])>0) foreach(array_keys($game["loc"][$loc[$i]]) as $j) if ((substr($j,0,5)=='user.') || substr($j,0,4)=='npc.') {if ($stmp=='') $stmp="\n<br/>�����: ".$loc[$i-1]; else $stmp.=", ".$loc[$i-1]; break;}
	};
$page_main.= $stmp;

// FIX: ��� ���� �����������: ����������, ���, ������, ��������
// �������
$stmp="";
$ind=0; $count=0; if(!$start) $start=0;
if ($game["loc"][$player["loc"]]) foreach (array_keys($game["loc"][$player["loc"]]) as $i) if ($i!=$login) {
	if ($ind>=$start && $ind<$start+$count_show) {	//FIX: ����� +1?
		// ��������� ������� �������� ��������� � �������/npc (������� ���-�� � ������)
		if (substr($i,0,5)=='item.') {
			$k=split("\|",$game["loc"][$player["loc"]][$i]);
			if (substr($i,0,11)!='item.stand.' && $k[1]>1) $k=$k[0]." (".$k[1].")"; else $k=$k[0];
			} else {
				$k=$game["loc"][$player["loc"]][$i]["title"];
				if ($game["loc"][$player["loc"]][$i]["life_max"]>0) $ltmp=round($game["loc"][$player["loc"]][$i]["life"]*100/$game["loc"][$player["loc"]][$i]["life_max"]);
				$st='';
				if ($ltmp<100) $st.=$ltmp."%";
				if ($game["loc"][$player["loc"]][$i]["ghost"]) $st.=" �������";
				if (substr($i,0,5)=='user.' && $game["loc"][$player["loc"]][$i]["crim"]) $st.=" ����������";
				$att=$game["loc"][$player["loc"]][$i]["attack"];
				if ($att && isset($game["loc"][$player["loc"]][$att]) && !$game["loc"][$player["loc"]][$att]["ghost"] && !$game["loc"][$player["loc"]][$i]["ghost"]) $st.=" ������� ".$game["loc"][$player["loc"]][$att]["title"];
				if ($st) {if ($st{0}==' ') $st=substr($st,1); $k.=" [".$st."]";}
				}

		$stmp.= "\n<br/><anchor>".$k."<go href=\"#menu\"><setvar name=\"to\" value=\"".$i."\"/></go></anchor>";
		}
	$ind++;
	}
if ($start) {$stmp.= "\n<br/><a href=\"$PHP_SELF?sid=$sid\">^ </a>";}
if ($start+$count_show<count($game["loc"][$player["loc"]])-1) {if (!$start) $stmp.="\n<br/>"; $stmp.= "<a href=\"$PHP_SELF?sid=$sid&start=".($start+$count_show)."\">+ (".(count($game["loc"][$player["loc"]])-1-$start-$count_show).")</a>";}
$page_main.=$stmp;

// EXITS
$page_main.= "\n<br/>---";
$loc=split("\|",$locations[$player["loc"]]);
for ($i=3;$i<count($loc);$i++) {
	if (substr($loc[$i],0,4)=='loc.') $page_main.= "\n<br/><a href=\"$PHP_SELF?sid=$sid&go=".$loc[$i]."\">".$loc[$i-1]."</a>";
	};
$page_main.="\n<br/><a href=\"$PHP_SELF?sid=$sid&look=1\">��������</a>";
if ($login==$admin) $page_main.="\n<br/><a href=\"$PHP_SELF?sid=$sid&adm=res\">res</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&adm=1\">admin</a>";

// ����� ����
$page_main.="\n</p>\n</card>\n<card id=\"menu\" title=\"����\">\n<p>\n<br/><a href=\"$PHP_SELF?sid=$sid&attack=$(to)\">���������</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&speak=$(to)\">��������</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&to=$(to)&list=inv\">�������</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&take=$(to)\">�����</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&look=$(to)\">����</a>";

msg($page_main,$loc[0],1,'main');

// ��������� �������

function savegame() {				// ���������� ����
	global $file_save,$game;
	if (isset($game["loc"]["loc.offline"])) {
		$file1 = fopen ("loc_offline.dat", "w");
		fputs($file1,serialize($game["loc"]["loc.offline"]));
		fclose ($file1);
		unset($game["loc"]["loc.offline"]);
		}
	rewind($file_save);	// � ������ �����
	fputs($file_save,serialize($game));
	fflush($file_save);
	fclose($file_save);
	}

function getrandname() {			// ���������� ��������� ���
	eval(implode('',file("f_getrandname.dat")));
	return $stmp;
	}

function addjournal($to,$msg) {		// ��������� � ������ � ������, ���� �� ������������
	global $game,$journal_count;
	if (isset($game["players"][$to])) {
		$j=&$game["loc"][$game["players"][$to]][$to]["journal"];
		$j[]=$msg;
		if (count($j)>$journal_count) array_splice($j,0,count($j)-$journal_count);	// ��������� ������ n ��������� �������
		}
	}
function addjournalall($loc,$msg,$no1="",$no2="") {		// ��������� ������ ���� � ������, ����� $no1 � $no2
	global $game;
	if ($game["loc"][$loc]) foreach (array_keys($game["loc"][$loc]) as $i) if ($i!=$no1 && $i!=$no2) if (isset($game["players"][$i])) addjournal($i,$msg);
	}

function msg($msg,$title='������ �������',$journal=1,$menu='') {//linkMsg		// ����� ������ � �����
	// journal==1, �� ������� ����� � ��������
	// menu=='', ������ "� ����" � "�����"
	// menu=='none', ��� ������
	// menu=='main', �������� ����
	global $game,$login,$page_size,$page_desc,$page_main,$debug,$PHP_SELF,$sid,$player,$page_size;

	if (!$debug) ob_start("translate");
	$wml = "\n<wml>";
	$wml.="\n<head>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"must-revalidate\"/>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"no-cache\"/>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"no-store\"/>\n</head>";
	// ������
	if ($journal==1 && $player["journal"] && count($player["journal"])>0) {		// FIX: ������-�� ���� ������ ������ ����� count=1
		$page_journal=implode("<br/>",$player["journal"]);
		$wml.= "\n<card title=\"������\">\n<do type=\"accept\" label=\"������\"><go href=\"#";
		if ($page_desc) $wml.= "desc";else $wml.= "main";
		$wml.= "\"/></do>\n<p>\n".$page_journal."\n</p>\n</card>";
		$player["journal"]=array();
		}

	$sizeok=1; 
	if ($player["look"]==$player["loc"]) {unset($player["look"]);$page_desc=0;}	// FIX: ����� ������� �������� �� ������ ������
	if ($page_desc) {
		$player["look"]=$player["loc"];
		eval(implode('',file("f_desc.dat")));
		if (strlen($wml.$msg.$desc[$player["loc"]])>$page_size) $sizeok=0;
		$wml.= "\n<card id=\"desc\" title=\"".$title."\">\n<do type=\"accept\" label=\"������\"><go href=\"";
		if ($sizeok) $wml.= "#main"; else $wml.= "$PHP_SELF?sid=$sid";
		$wml.= "\"/></do>\n<p>\n".$desc[$player["loc"]]."\n</p>\n</card>";
		}

	// �������� �����
	if ($sizeok) {		// ������ ���� ������ ������ ��� �����
	$wml.= "\n<card id=\"main\" title=\"".$title."\""; 
	if ($menu=='main') $wml.= " ontimer=\"$PHP_SELF?sid=$sid\"><timer value=\"600\"/";
	$wml.= ">";
	if ($menu=='') {
		$wml.= "\n<do name=\"o1\" type=\"options\" label=\"� ����\"><go href=\"$PHP_SELF?sid=$sid\"/></do>";
		$wml.= "\n<do name=\"a1\" type=\"accept\" label=\"�����\"><prev/></do>";
		}
	if ($menu=='main') {
		$wml.= "\n<do name=\"o2\" type=\"options\" label=\"��������\"><go href=\"$PHP_SELF?sid=$sid&list=inv\"/></do>";
		$wml.= "\n<do name=\"o3\" type=\"options\" label=\"�����\"><go href=\"$PHP_SELF?sid=$sid&list=magic\"/></do>";
		$wml.= "\n<do name=\"o5\" type=\"options\" label=\"��������\"><go href=\"$PHP_SELF?sid=$sid&msg=1\"/></do>";
		$j=6;
		for ($i=1;$i<10;$i++) if (isset($player["macros"][$i])) {$wml.= "\n<do name=\"o".$j."\" type=\"options\" label=\"".$player["macros"][$i]["name"]."\"><go href=\"$PHP_SELF?sid=$sid&macros=".$i."\"/></do>"; $j++;}
		$wml.= "\n<do name=\"o".$j."\" type=\"options\" label=\"[�������]\"><go href=\"$PHP_SELF?sid=$sid&macros=list\"/></do>";
		}

	if (substr($msg,strlen($msg)-4)!="</p>") $msg.="\n</p>";
	if (substr($msg,0,2)!="<p") $msg="<p>\n".$msg;
	$wml.= "\n".$msg."\n</card>";
	};// if sizeok

	$wml.= "</wml>";
	$wml=str_replace("&amp;","&",$wml);		// ����� �������� � ������ ����
	$wml=str_replace("&","&amp;",$wml);
	savegame();									// ����� ���� ��������� �������, ������ ����� ������
	if ($debug) echo "\n<html><title>".$player["loc"]." : ".$title." : ".strlen($wml)."</title>";
	echo $wml;
	if ($debug) {
		echo "<p>�������:<br>";
		if ($game["loc"][$player["loc"]]) foreach(array_keys($game["loc"][$player["loc"]]) as $i) if ($i!=$login) echo "<br/>".$i;
		echo "<p><a href=\"$PHP_SELF?sid=$sid&list=inv\">���������:</a><br>";
		if ($player["items"]) foreach(array_keys($player["items"]) as $i) echo "<br/>".$i;
		echo "<p><a href=\"$PHP_SELF?sid=$sid&list=magic\">�����</a>";
		}
	if (!$debug) ob_end_flush();
	die("");					// ������ ������� ���������
	}

function ai() {		// ����� AI			//linkAI
	global $game,$locations,$login,$player,$time_logout;

	// ��� � 12 ����� ������������
	if (time()>$game["lastcopy"]+43200) eval(implode('',file("f_support.dat")));

	// �������� ������ ������ � ��������� ���� ����� �� ����
	if (time()>$game["lastai"]+60) {
	foreach(array_keys($game["players"]) as $j) if ($j!=$login) { 	// ��� � ������
		if (time()>$game["loc"][$game["players"][$j]][$j]["time"]+$time_logout) {
			if (isset($game["loc"][$game["players"][$j]][$j])) {
				// � �������
				if (!isset($game["loc"]["loc.offline"])) eval(implode('',file("f_loadoffline.dat")));	// ���� ���, �������� (���� ���)
				$game["loc"]["loc.offline"][$j]=$game["loc"][$game["players"][$j]][$j];
				$game["loc"]["loc.offline"][$j]["journal"]=array();
				$game["loc"]["loc.offline"][$j]["loc"]=$game["players"][$j];
				unset($game["loc"][$game["players"][$j]][$j]);
				addjournalall($game["players"][$j],$game["loc"]["loc.offline"][$j]["title"]." �����",$j);
				unset($game["players"][$j]);
				} else unset($game["players"][$j]);
			}
		}
	$game["lastai"]=time();
	}

	if (!$login || !$player) return;	// ��� ����� ������ ������� ������ ������

	// ��������� ������ ������� � �������� �������
	doai($player["loc"]);
	$ok=array($player["loc"]=>1);	// ��� ���������
	$loc=split("\|",$locations[$player["loc"]]);
	for ($i=3;$i<count($loc);$i++) if (substr($loc[$i],0,4)=='loc.') {
		doai($loc[$i]);
		$ok[$loc[$i]]=1;
		$loc1=split("\|",$locations[$loc[$i]]);
		for ($j=3;$j<count($loc1);$j++) if (substr($loc1[$j],0,4)=='loc.') if (!isset($ok[$loc1[$j]])) {doai($loc1[$j]); $ok[$loc1[$j]]=1;}
		}
	}

function doai($i) {				// ������������� ���������, ��������� ������� � ������ $i
	global $game,$locations,$time_logout,$time_regenerate,$time_objects_destroy,$time_crim;

	$loc=split("\|",$locations[$i]);

	// ������� �������
	if (isset($game["loc_del"][$i])) foreach (array_keys($game["loc_del"][$i]) as $j) {
		if (time()>$game["loc_del"][$i][$j]) {	// �������� ��������/npc
			if (substr($j,0,4)=='npc.') addjournalall($i,$game["loc"][$i][$j]["title"]." �����");
			unset($game["loc"][$i][$j]);
			unset($game["loc_del"][$i][$j]);
			if (count($game["loc_del"][$i])==0) unset($game["loc_del"][$i]);
			}
		}
	if (isset($game["loc_add"][$i])) foreach (array_keys($game["loc_add"][$i]) as $j) {
		if (time()>$game["loc_add"][$i][$j]["time"]) {	// ���������� ��������/npc
			if ($game["loc_add"][$i][$j]["respawn"]) {
				$respawn=split("\|",$game["loc_add"][$i][$j]["respawn"]);
				$game["loc_add"][$i][$j]["time"]=time()+rand($respawn[0],$respawn[1]);
				if ($respawn[2] && $respawn[3] && substr($j,0,5)=='item.') {	// ������� ���-��
					$item=split("\|",$game["loc_add"][$i][$j]["item"]);
					$item[1]=rand($respawn[2],$respawn[3]);
					$game["loc_add"][$i][$j]["item"]=implode("|",$item);
					}
				}
			$game["loc"][$i][$j]=$game["loc_add"][$i][$j]["item"];
			if (substr($j,0,4)=='npc.') {
				addjournalall($i,"�������� ".$game["loc_add"][$i][$j]["item"]["title"]);
				unset($game["loc_add"][$i][$j]);	// npc �������, ��� ��������� ������ ��������� �����
				if (count($game["loc_add"][$i])==0) unset($game["loc_add"][$i]); 
				}
			}
		}

	// ������ ������, ���� ����� �����
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (substr($j,0,9)=='npc.guard') if (time()>$game["loc"][$i][$j]["delete"]) {unset($game["loc"][$i][$j]); addjournalall($i,$game["loc"][$i][$j]["title"]." �����");}

	// ���� �� ������, ���� �� �����, ������ ������ (������� � ���� ������) � ������ �������
	$crim=array();
	$users=array();
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (substr($j,0,5)=='user.' || substr($j,0,4)=='npc.') {
		if ($game["loc"][$i][$j]["healer"]) $healer=$game["loc"][$i][$j]["title"];
		if (substr($j,0,9)=='npc.crim.' || $game["loc"][$i][$j]["crim"]) if (!$game["loc"][$i][$j]["ghost"]) $crim[]=$j;	// ������-��������� �� ���������
		if (substr($j,0,9)=="npc.guard") $guard=1;
		if (substr($j,0,5)=="user." && !$game["loc"][$i][$j]["ghost"]) $users[]=$j;
		}
	// ��������� ������ �� 1 �� 3 ������
	if ($loc[1] && count($crim)>0 && !$guard) for ($k=0;$k<rand(1,3);$k++) {	
		srand ((float) microtime() * 10000000);
		$id = "npc.guard.".rand(5,9999);
		$title = getrandname()." [������]";
		$game["loc"][$i][$id]=array("title"=>$title,"life"=>"1000","life_max"=>"1000","speak"=>"npc.guard","war"=>"100|100|100|2|0|10|20|0|0|10|30|40|���������|0||","delete"=>time()+$time_logout);
		//$game["loc_del"][$i][$id]=time()+$time_logout;	// ����� ������� ������
		addjournalall($i,"�������� ".$title);
		}

	// ������ ���������� ������� � npc
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (isset($game["loc"][$i][$j]) && (substr($j,0,5)=='user.' || substr($j,0,4)=='npc.')) {
		// ����������� ����� � ���� �������� ���������� �������
		$tm=time()-$game["loc"][$i][$j]["time_regenerate"];
		if ($tm>$time_regenerate && !$game["loc"][$i][$j]["ghost"]) {
			$life=0; $mana=0;
			if (substr($j,0,5)=='user.') {	// �������� ������ ����������� � ���������
				$skills=split("\|",$game["loc"][$i][$j]["skills"]);
				$life=$skills[16];
				$mana=$skills[5];
				}
			$game["loc"][$i][$j]["life"]+=round($tm/($time_regenerate-$life));
			$game["loc"][$i][$j]["mana"]+=round($tm/($time_regenerate-$mana));
			if ($game["loc"][$i][$j]["life"]>$game["loc"][$i][$j]["life_max"]) $game["loc"][$i][$j]["life"]=$game["loc"][$i][$j]["life_max"];
			if ($game["loc"][$i][$j]["mana"]>$game["loc"][$i][$j]["mana_max"]) $game["loc"][$i][$j]["mana"]=$game["loc"][$i][$j]["mana_max"];
			$game["loc"][$i][$j]["time_regenerate"]=time();
			}

		// ������
		if (substr($j,0,5)=="user.") {
			// ��������, �� ������ �� ����� �����
			if (time()>$game["loc"][$i][$j]["time_crim"]) {unset($game["loc"][$i][$j]["crim"]); unset($game["loc"][$i][$j]["time_crim"]);}
			// ���� ���� ������, �� �����������...
			if ($game["loc"][$i][$j]["ghost"] && $healer) {addjournalall($i,$healer.": ����������� � �����, ".$game["loc"][$i][$j]["title"]."!");ressurect($j);}
			}

		// NPC
		if (substr($j,0,4)=='npc.') {
			$b=0;	// ���� �� continue, ���� ���� � ��. �������
			// ������ ����� ������� �� ��������
			$owner=$game["loc"][$i][$j]["owner"];
			$follow=$game["loc"][$i][$j]["follow"];
			$guard=$game["loc"][$i][$j]["guard"];
			$attack=$game["loc"][$i][$j]["attack"];
			if ($owner) {
				// ������ ����� ���� ����
				if ($game["loc"][$i][$j]["crim"] && isset($game["loc"][$i][$owner])) docrim($owner);
				// ���� ����� ����� ��������
				if (time()>$game["loc"][$i][$j]["time_owner"]) {
					addjournal($owner,$game["loc"][$i][$j]["title"]." ������� ���");
					if ($game["loc"][$i][$j]["destroyonfree"]) {addjournalall($i,$game["loc"][$i][$j]["title"]." �����"); unset($game["loc"][$i][$j]); continue;}	// ������ �� ������������ ��� 
						else {unset($game["loc"][$i][$j]["time_owner"]); unset($game["loc"][$i][$j]["owner"]);unset($game["loc"][$i][$j]["follow"]); unset($game["loc"][$i][$j]["guard"]);}
					}
				}
			if ($follow && !isset($game["loc"][$i][$follow])) for ($k=3;$k<count($loc);$k++) if (substr($loc[$k],0,4)=='loc.' && isset($game["loc"][$loc[$k]][$follow])) {
				// ����� � �������� ������� $follow, ���� ����
				$game["loc"][$loc[$k]][$j] = $game["loc"][$i][$j];
				unset($game["loc"][$i][$j]);
				unset($game["loc"][$k][$j]["attack"]);
				addjournalall($i,$game["loc"][$loc[$k]][$j]["title"]." ���� ".$loc[$k-1]);
				addjournalall($loc[$k],"������ ".$game["loc"][$loc[$k]][$j]["title"]);
				$b=1;	// ������ �� ������������ � ������� �������
				break;
				}
			if ($b) continue;		//$j ���� �� ���� �������

			// �������� ������������ (���� �� �� ��� �� �������)
			if ($attack && !$game["loc"][$i][$j]["follow"] && !isset($game["loc"][$i][$attack])) for ($k=3;$k<count($loc);$k++) if (substr($loc[$k],0,4)=='loc.' && isset($game["loc"][$loc[$k]][$attack])) {	// �����!
				// ������� �� ����� ������������ � ����������� ����, � ������ �� ������� � ���� ����, � ����� ������ ����������!
				$crimj=$game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.';
				$loc1=split("\|",$locations[$loc[$k]]);
				$b=0;	
				if (($crimj && !$loc1[1]) || (!$crimj && $loc1[1]) || substr($j["id"],0,9)=="npc.guard") $b=1;	// ���������� ������
				// �������� ����� ������ skill.hiding, ����� ��������� (�� ������ �� ���������)
				if (substr($attack,0,5)=='user.' && !substr($j,0,9)=="npc.guard") {
					$skills=split("\|",$game["loc"][$loc[$k]][$attack]);
					if (rand(0,100)<=($skills[17]+$skills[1])*10) {$b=0;addjournal($attack,"�� �������� �� ������!");}
					}

				if ($b) {	// ������!
					$game["loc"][$loc[$k]][$j] = $game["loc"][$i][$j];
					unset($game["loc"][$i][$j]);
					addjournalall($i,$game["loc"][$loc[$k]][$j]["title"]." ���� ".$loc[$k-1]);
					addjournalall($loc[$k],"������ ".$game["loc"][$loc[$k]][$j]["title"]);
					} else unset($game["loc"][$i][$j]["attack"]);
				break;
				}
			if ($b) continue;		//$j ���� �� ���� �������
			// ���� �� ����, ���� �������� guard=id ���-�� ��������, ������� ���
			if ($guard && isset($game["loc"][$i][$guard])) {
				if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $k) if ($game["loc"][$i][$k]["attack"]==$guard) {$game["loc"][$i][$j]["attack"]=$k; break;}
				}

			// ����� ������� ������, ����� �������
			if (!$game["loc"][$i][$j]["attack"]) {
				if (substr($j,0,9)=="npc.guard" && count($crim)>0) $game["loc"][$i][$j]["attack"]=$crim[rand(0,count($crim)-1)];
				if (($game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.') && count($users)>0) {
					$b=0;
					$attack=$users[rand(0,count($users)-1)];
					if (substr($attack,0,5)=='user.') {$skills=split("\|",$game["loc"][$i][$attack]); if (rand(0,100)<=$skills[1]*5) {$b=1;addjournal($attack,"�� �������� �� ����� ".$game["loc"][$i][$j]["title"]);}}
					if (!$b) $game["loc"][$i][$j]["attack"]=$attack;
					}

				// ���� ��� ��� �� �������, �� ������� ����, ��� ������� ���
				//if (!$game["loc"][$i][$j]["attack"]) if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $k) if ($game["loc"][$i][$k]["attack"]==$j) {$game["loc"][$i][$j]["attack"]=$k; break;}
				}

			// ��������� ��������� �������� NPC
			if (!$game["loc"][$i][$j]["attack"] && $game["loc"][$i][$j]["move"]) {
				$move=split("\|",$game["loc"][$i][$j]["move"]);
				$b=0;
				if (time()>$game["loc"][$i][$j]["time_nextmove"]) {	// ����...
					$k=$loc[2+2*rand(0,(count($loc)-2)/2-1)+1];	// ��������� �����
					// ������ �� ���� � ���� ����, � ������� �� ���
					$crimj=$game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.';
					$loc1=split("\|",$locations[$loc[$k]]);
					if (($crimj && !$loc1[1]) || (!$crimj && $loc1[1])) $b=1;	// ����
					if ($k==$i) $b=0;
					if ($b) {
						// �������
						$game["loc"][$k][$j]=$game["loc"][$i][$j];
						unset($game["loc"][$i][$j]);
						addjournalall($k,"������ ".$game["loc"][$k][$j]["title"]);
						$s=$game["loc"][$k][$j]["title"]." ���� ";
						if (array_search($k,$loc)) $s.=$loc[array_search($k,$loc)-1];
						addjournalall($i,$s);
						$game["loc"][$k][$j]["time_nextmove"]=time()+rand($move[1],$move[2]);	// ����. ���
						}
					}
				}
			if ($b) continue;		//$j ���� �� ���� �������
			// ��������� ����� NPC
			if ($game["loc"][$i][$j]["attack"] && $game["loc"][$i][$game["loc"][$i][$j]["attack"]]["ghost"]) unset($game["loc"][$i][$j]["attack"]);
			if ($game["loc"][$i][$j]["attack"]) attack($i,$j,$game["loc"][$i][$j]["attack"]);
			}//npc		
		}//foreach user & npc

	// ������ ������ ������
	//if (count($game["loc"][$i])==0) unset($game["loc"][$i]);
	}

function ressurect($to) {
	eval(implode('',file("f_ressurect.dat")));
	}
function docrim($login) {
	eval(implode('',file("f_docrim.dat")));
	}
function calcparam($login) {
	eval(implode('',file("f_calcparam.dat")));
	};

function attack($loc,$fromid,$toid,$magic='',$answer=1) {//linkAttack		// answer=1 - ������������� ��������, 0 -���
	global $attackf;
	global $game,$locations,$login,$time_crim,$points_levelup,$time_objects_destroy,$time_logout,$time_defspeed;
	if (!$attackf) $attackf=implode('',file("f_attackf.dat"));
	eval($attackf);
	}

function view($file) {eval(implode('',file("f_view.dat")));}