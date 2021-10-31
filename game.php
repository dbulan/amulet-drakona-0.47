<?php

$version = 0.47;
if (file_exists("flag_debug")) $debug=1;
if (file_exists("flag_usesession")) $usesession=1;

$admin = "user.admin";								// логин админа, пароль задайте при регистрации
$gm_id = "";									// если game.php?gm=12345 - это для полного доступа к игре через Internet Exploorer без логина. Если пустой, то отключено.

// настройки по умолчанию
$game_title = "Амулет Дракона";						// название игры
$game_file = "game.dat";							// файл для сохранения игры
$time_logout = 5*60;								// если столько секунд клиент не отвечает, считаем его оффлайн
$time_objects_destroy = 10*60;						// таймаут для валяющиюхся предметов, после кот. они уничтожаются
$time_crim = 10*60;								// время, сколько человек остается криминалом
$time_regenerate = 30;								// время регенерации жизни и маны на единицу (без учета навыков регенерации и медитации)
$points_limit_attr = 12;							// лимит очков на str.dex.int
$points_limit_attr_one=5;							// максимальное значение str,dex,int
$points_limit_skills=40;							// лимит очков на скиллы
$points_limit_skills_one=5;							// максим. значение одного скилла
$points_levelup = 5;								// коэфф., на который умножается сумма очков аттрибутов и навыков для перехода на след. уровень. При переходе текущий опыт обнуляется и добавляется 1 очко опыта
$time_defspeed = 4;								// по умолчанию перерыв между атаками монстров 4 секунды
$count_show = 5;									// такое кол-во объектов показывать на экране за раз
$page_size = 1400;								// не более стольких символом должна получиться wml
$journal_count=5;									// кол-во записей в журнале

// обнуляем переменные
$page_main = "";
$page_desc = "";

if ($PHP_SELF=='') $PHP_SELF = $_SERVER["PHP_SELF"];			// путь к текущему файлу
$tmp=$QUERY_STRING;if($tmp=='') $tmp=$_SERVER["QUERY_STRING"];	// в зависимости от настроек сервера
$tmp=urldecode($tmp);	
$tmp=preg_replace("/[^ -}А-я]/e","",$tmp);				// удаляем нечитабельные символы
parse_str($tmp);									// теперь появятся переменные $look, $attack и т.д.
if ($gm_id && $gm==$gm_id) $debug=1;

if (file_exists("flag_update") && (($gm_id &&$gm!=$gm_id) || !$gm_id))  eval(implode('',file("f_update.dat")));

$locations=array(
	"loc.0"=>		"Welcome|1|к лекарю|loc.lek1|к магазину драгоценностей|loc.drag1|по дороге на юг|loc.sklad1",
	"loc.lek1"=>	"Двор лекаря|1|войти в дом|loc.lek|к Академии|loc.ak1|к банку|loc.bank1|на юг|loc.0",
	"loc.lek"=>		"Дом лекаря|1|выйти на улицу|loc.lek1",
	"loc.drag1"=>	"Перед магазином драгоценностей|1|войти в дом|loc.drag|на восток|loc.tenal|на запад|loc.0",
	"loc.drag"=>	"Магазин драгоценностей|1|выйти на улицу|loc.drag1",
	"loc.sklad1"=>	"Дорога к складу|1|к складу|loc.sklad2|к южным воротам|loc.uv|на север|loc.0",
	"loc.sklad2"=>	"Около склада|1|войти в склад|loc.sklad|войти в магазин|loc.reg|на восток|loc.jd3|на запад|loc.sklad1",
	"loc.sklad"=>	"На складе|0|выйти на улицу|loc.sklad2",
	"loc.reg"=>		"Магазин реагентов|1|выйти на улицу|loc.sklad2",
	"loc.jd3"=>		"Жилые дома|1|к складу|loc.sklad2|на восток|loc.jd4|на север|loc.jd1",
	"loc.jd4"=>		"Трущобы|0|на запад|loc.jd3|к особняку|loc.jd2",
	"loc.jd2"=>		"Около особняка|1|к особняку|loc.osobn|на север|loc.tenal|на запад|loc.jd1|в трущобы на юге|loc.jd4",
	"loc.jd1"=>		"Жилые дома|1|к особняку|loc.jd2|к тенистой аллее|loc.tenal|на юг|loc.jd3",
	"loc.osobn"=>	"Особняк|1|выйти из особняка|loc.jd2",
	"loc.tenal"=>	"Тенистая аллея|1|восточные ворота|loc.vv|к магазину|loc.drag1|в жилой район|loc.jd1|в жилой район на восток|loc.jd2",
	"loc.vv"=>		"Восточные ворота|1|выйти из города|loc.zvv|на площадь|loc.vpl|к Академии|loc.ak1|к тенистой аллее|loc.tenal",
	"loc.vpl"=>		"Восточная площадь|1|к восточным воротам|loc.vv",
	"loc.ak1"=>		"Перед Академией|1|войти в Академию|loc.ak|к конюшне|loc.kon1|к восточным воротам|loc.vv|к дому лекаря|loc.lek1|к банку|loc.bank1",
	"loc.bank1"=>	"Перед банком|1|войти в банк|loc.bank|к Академии|loc.ak1|к лекарю|loc.lek1|на юг|loc.centr1|на запад|loc.cpl",
	"loc.bank"=>	"В банке|1|южная дверь|loc.bank1|западная дверь|loc.bank2",
	"loc.bank2"=>	"Около банка|1|войти в банк|loc.bank|к конюшне|loc.kon1|на площадь|loc.cpl|к северным воротам|loc.sv",
	"loc.kon1"=>	"Около конюшни|1|войти в конюшню|loc.kon|к Академии|loc.ak1|к банку|loc.bank2",
	"loc.kon"=>		"В конюшне|1|выйти на улицу|loc.kon1",
	"loc.centr1"=>	"Центральная дорога|1|к банку|loc.bank1|войти в таверну|loc.tav|подойти к кузнецу|loc.kuzn|войти в двойное здание|loc.br|на юг|loc.centr2",
	"loc.tav"=>		"Таверна|1|выйти из таверны|loc.centr1|подойти к барной стойке|loc.tav1|в дальний угол|loc.tav2",
	"loc.tav1"=>	"Таверна|1|на второй этаж|loc.tav3|в дальний угол|loc.tav2|к выходу из таверны|loc.tav",
	"loc.tav2"=>	"Таверна|1|на второй этаж|loc.tav3|подойти к барной стойке|loc.tav1|к выходу из таверны|loc.tav",
	"loc.tav3"=>	"Таверна|1|войти в первую дверь|loc.tav4|войти во вторую дверь|loc.tav5|южная лестница на первый этаж|loc.tav1|северная лестница на первый этаж|loc.tav2",
	"loc.tav4"=>	"Таверна|1|выйти в корридор|loc.tav3",
	"loc.tav5"=>	"Таверна|1|выйти в корридор|loc.tav3",
	"loc.br"=>		"Магазин брони|1|выйти на улицу|loc.centr1|перейти в дверь на юге|loc.or",
	"loc.or"=>		"Магазин оружия|1|выйти на улицу|loc.centr2|перейти в дверь на севере|loc.br",
	"loc.centr2"=>	"Центральная улица|1|подойти к кузнецу|loc.kuzn|войти в двойное здание|loc.or|к южным воротам|loc.uv|на север|loc.centr1",
	"loc.kuzn"=>	"Кузница|1|войти в северную дверь|loc.br|войти в южную дверь|loc.or|на север|loc.centr1|на юг|loc.centr2",
	"loc.uv"=>		"Южные ворота|1|на север|loc.centr2|на восток|loc.sklad1|на запад|loc.uz2|выйти за город|loc.pristan",
	"loc.uz2"=>		"Торговый квартал|1|войти в магазин припасов|loc.prip|войти в магазин на юге|loc.luk|на восток к южным воротам|loc.uv|на запад|loc.uz1",
	"loc.prip"=>	"Магазин припасов|1|выйти на улицу|loc.uz2",
	"loc.luk"=>		"Магазин для лучников|1|выйти на улицу|loc.uz2",
	"loc.uz1"=>		"Торговый квартал|1|войти в магазин на севере|loc.jiv|войти в магазин на юге|loc.but|на запад|loc.kaz1|на восток|loc.uz2",
	"loc.jiv"=>		"Магазин Животные|1|выйти на улицу|loc.uz1",
	"loc.but"=>		"Магазин напитков|1|выйти на улицу|loc.uz1",
	"loc.kaz1"=>	"Перед казармами|1|войти в казарму|loc.kaz|к березовой роще|loc.br3|на запад|loc.dv1|на восток|loc.uz1",
	"loc.kaz"=>		"Казармы|1|выйти на улицу|loc.kaz1",
	"loc.dv1"=>		"Около старого дома|1|войти в старый дом|loc.dv|к березовой роще|loc.br3|на восток|loc.kaz1",
	"loc.dv"=>		"Старый дом|0|выйти на улицу|loc.dv1|дверь в углу|loc.dv2",
	"loc.dv2"=>		"Старый дом|0|выйти из комнаты|loc.dv",
	"loc.br3"=>		"Березовая роща|0|к старому дому|loc.dv1|к казармам|loc.kaz1|на запад|loc.br4|на север|loc.br1",
	"loc.br4"=>		"Березовая роща|0|на восток|loc.br3|на север|loc.br2",
	"loc.br2"=>		"Березовая роща|0|на восток|loc.br1|на юг|loc.br4",
	"loc.br1"=>		"Березовая роща|1|к северным воротам|loc.sv|тропинка на запад|loc.br2|тропинка на юг|loc.br3",
	"loc.sv"=>		"Северные ворота|1|выйти из города|loc.zsv|войти в здание|loc.snar|к банку|loc.bank2|к березовой роще|loc.br1",
	"loc.snar"=>	"Магазин снаряжения|1|выйти из здания|loc.sv",
	"loc.zvv"=>		"За восточными воротами|0|войти в город|loc.vv|дорога на север|loc.vd.1|лес на востоке|loc.vl.18",
	"loc.zsv"=>		"За северными воротами|0|войти в город|loc.sv|дорога на север|loc.sd.1|тропинка на запад|loc.zb.1",
	"loc.zb.1"=>	"Западный берег|0|к северным воротам|loc.zsv|лес на север|loc.zl.3|тропа на юг|loc.zb.2",
	"loc.zb.2"=>	"Западный берег|0|тропа на север|loc.zb.1|тропа на юг|loc.zb.3",
	"loc.zb.3"=>	"Западный берег|0|тропа на севере|loc.zb.2|тропа на юг|loc.zb.4",
	"loc.zb.4"=>	"Западный берег|0|тропа на север|loc.zb.3|тропа на восток|loc.zb.5",
	"loc.zb.5"=>	"Западный берег|0|тропа на запад|loc.zb.4|пристань на восток|loc.pristan",
	"loc.pristan"=>	"Пристань|1|войти в город|loc.uv|тропа на запад|loc.zb.5|в порт|loc.port1",
	"loc.port1"=>	"Порт|1|на пристань|loc.pristan|на восток|loc.port2",
	"loc.port2"=>	"Порт|0|в лес на востоке|loc.bl.1|на запад|loc.port1",
	"loc.ak"=>		"Академия|1|выйти на улицу|loc.ak1|войти в магазин|loc.ak4|в библиотеку|loc.ak2|в зал монстрологии|loc.ak5|на второй этаж|loc.ak3",
	"loc.ak4"=>		"Волшебный магазин|1|выйти в парадный зал|loc.ak",
	"loc.ak2"=>		"Библиотека|1|выйти в парадный зал|loc.ak",
	"loc.ak5"=>		"Зал монстрологии|1|выйти в парадный зал|loc.ak",
	"loc.ak3"=>		"Академия|1|спуститься на первый этаж|loc.ak",
	"loc.cpl"=>		"Центральная площадь|1|к банку на север|loc.bank2|к банку на восток|loc.bank1|во двор рыцарей|loc.dvr",
	"loc.dvr"=>		"Двор рыцарей|1|выйти на площадь|loc.cpl|войти в главное здание|loc.dvr4|войти в здание на севере|loc.dvr2|к ристалищу|loc.dvr1",
	"loc.dvr2"=>	"Двор рыцарей|1|выйти во двор|loc.dvr",
	"loc.dvr4"=>	"Двор рыцарей|1|выйти во двор|loc.dvr",
	"loc.dvr1"=>	"Ристалище|1|выйти во двор|loc.dvr|подойти к мечникам|loc.dvr5|подойти к мишеням|loc.dvr3",
	"loc.dvr5"=>	"Ристалище|1|ко входу в ристалище|loc.dvr1|подойти к мишеням|loc.dvr3",
	"loc.dvr3"=>	"Ристалище|1|ко входу в ристалище|loc.dvr1|подойти к мечникам|loc.dvr5",
	"loc.bl.1"=>	"Болотный лес|0|север|loc.bl.3|восток|loc.bl.2|на запад в порт|loc.port2",
	"loc.bl.2"=>	"Болотный лес|0|север|loc.bl.4|восток|loc.vl.1|запад|loc.bl.1",
	"loc.bl.3"=>	"Болотный лес|0|север|loc.bl.5|восток|loc.bl.4|юг|loc.bl.1",
	"loc.bl.4"=>	"Болотный лес|0|север|loc.bl.6|восток|loc.vl.4|юг|loc.bl.2|запад|loc.bl.3",
	"loc.bl.5"=>	"Болотный лес|0|север|loc.bl.7|восток|loc.bl.6|юг|loc.bl.3",
	"loc.bl.6"=>	"Болотный лес|0|север|loc.bl.8|перейти овраг восток|loc.vl.7|юг|loc.bl.4|на запад|loc.bl.5",
	"loc.bl.7"=>	"Болотный лес|0|север|loc.vl.13|восток|loc.bl.8|юг|loc.bl.5",
	"loc.bl.8"=>	"Болотный лес|0|север|loc.vl.14|перейти овраг на восток|loc.vl.10|юг|loc.bl.6|запад|loc.bl.7",
	"loc.kl.1"=>	"Кладбище|0|выйти на дорогу|loc.vd.2|войти в калитку|loc.kl.8|восток|loc.kl.2|запад|loc.kl.15",
	"loc.kl.2"=>	"Кладбище|0|север|loc.kl.7|вдоль ограды на восток|loc.kl.3|к калитке на запад|loc.kl.1",
	"loc.kl.3"=>	"Кладбище|0|войти в усыпальницу|loc.kl.4|вдоль ограды на запад|loc.kl.2|север|loc.kl.6",
	"loc.kl.4"=>	"Кладбище|0|выйти|loc.kl.3",
	"loc.kl.5"=>	"Кладбище|0|запад|loc.kl.6",
	"loc.kl.6"=>	"Кладбище|0|север|loc.kl.24|восток|loc.kl.5|юг|loc.kl.3|запад|loc.kl.7",
	"loc.kl.7"=>	"Кладбище|0|север|loc.kl.23|восток|loc.kl.6|юг|loc.kl.2",
	"loc.kl.8"=>	"Кладбище|0|войти в здание|loc.kl.20|выйти в калитку на юге|loc.kl.1",
	"loc.kl.9"=>	"Кладбище|0|север|loc.kl.19|юг|loc.kl.15|запад|loc.kl.10",
	"loc.kl.10"=>	"Кладбище|0|север|loc.kl.18|восток|loc.kl.9|юг|loc.kl.14",
	"loc.kl.11"=>	"Кладбище|0|вдоль забора на север|loc.kl.16|вдоль забора на юг|loc.kl.12",
	"loc.kl.12"=>	"Кладбище|0|вдоль забора на север|loc.kl.11|вдоль забора на восток|loc.kl.13",
	"loc.kl.13"=>	"Кладбище|0|вдоль забора восток|loc.kl.14|вдоль забора запад|loc.kl.12",
	"loc.kl.14"=>	"Кладбище|0|север|loc.kl.10|восток|loc.kl.15|запад|loc.kl.13",
	"loc.kl.15"=>	"Кладбище|0|север|loc.kl.9|восток|loc.kl.1|запад|loc.kl.14",
	"loc.kl.16"=>	"Кладбище|0|север|loc.kl.33|восток|loc.kl.17|юг|loc.kl.11",
	"loc.kl.17"=>	"Кладбище|0|север|loc.kl.32|восток|loc.kl.18|запад|loc.kl.16",
	"loc.kl.18"=>	"Кладбище|0|север|loc.kl.31|восток|loc.kl.19|юг|loc.kl.10|запад|loc.kl.17",
	"loc.kl.19"=>	"Кладбище|0|север|loc.kl.30|юг|loc.kl.9|запад|loc.kl.18",
	"loc.kl.20"=>	"Кладбище|0|войти в северную дверь|loc.kl.22|войти в восточную дверь|loc.kl.21|выйти на улицу|loc.kl.8",
	"loc.kl.21"=>	"Кладбище|0|выйти|loc.kl.20",
	"loc.kl.22"=>	"Кладбище|0|выйти|loc.kl.20",
	"loc.kl.23"=>	"Кладбище|0|север|loc.kl.28|восток|loc.kl.24|юг|loc.kl.7",
	"loc.kl.24"=>	"Кладбище|0|войти в усыпальницу|loc.kl.25|север|loc.kl.27|юг|loc.kl.6|запад|loc.kl.23",
	"loc.kl.25"=>	"Кладбище|0|выйти на улицу|loc.kl.24",
	"loc.kl.26"=>	"Кладбище|0|запад|loc.kl.27",
	"loc.kl.27"=>	"Кладбище|0|север|loc.kl.42|восток|loc.kl.26|юг|loc.kl.24|запад|loc.kl.28",
	"loc.kl.28"=>	"Кладбище|0|войти в усыпальницу|loc.kl.40|восток|loc.kl.27|юг|loc.kl.23|запад|loc.kl.29",
	"loc.kl.29"=>	"Кладбище|0|север|loc.kl.39|восток|loc.kl.28|запад|loc.kl.30",
	"loc.kl.30"=>	"Кладбище|0|войти в строение|loc.kl.37|восток|loc.kl.29|юг|loc.kl.19|запад|loc.kl.31",
	"loc.kl.31"=>	"Кладбище|0|север|loc.kl.36|восток|loc.kl.30|юг|loc.kl.18|запад|loc.kl.32",
	"loc.kl.32"=>	"Кладбище|0|север|loc.kl.35|восток|loc.kl.31|юг|loc.kl.17|запад|loc.kl.33",
	"loc.kl.33"=>	"Кладбище|0|север|loc.kl.34|восток|loc.kl.32|юг|loc.kl.16",
	"loc.kl.34"=>	"Кладбище|0|вдоль ограды на восток|loc.kl.35|вдоль ограды на юг|loc.kl.33",
	"loc.kl.35"=>	"Кладбище|0|восток|loc.kl.36|юг|loc.kl.32|запад|loc.kl.34",
	"loc.kl.36"=>	"Кладбище|0|юг|loc.kl.31|запад|loc.kl.35",
	"loc.kl.37"=>	"Кладбище|0|войти в дверь|loc.kl.38|выйти на улицу|loc.kl.30",
	"loc.kl.38"=>	"Кладбище|0|выйти|loc.kl.37",
	"loc.kl.39"=>	"Кладбище|0|юг|loc.kl.29",
	"loc.kl.40"=>	"Кладбище|0|войти в дверь|loc.kl.41|выйти на улицу|loc.kl.28",
	"loc.kl.41"=>	"Кладбище|0|выйти|loc.kl.40",
	"loc.kl.42"=>	"Кладбище|0|войти в склеп|loc.kl.43|юг|loc.kl.27",
	"loc.kl.43"=>	"Кладбище|0|выйти на улицу|loc.kl.42",
	"loc.sd.1"=>	"Северная дорога|0|дорога на север|loc.sd.2|к северным воротам|loc.zsv|к озеру на восток|loc.sl.1|лес на западе|loc.zl.1",
	"loc.sd.2"=>	"Северная дорога|0|войти в дом|loc.kzd|дорога на север|loc.sd.3|дорога на юг|loc.sd.1|на запад|loc.zl.10",
	"loc.sd.3"=>	"Северная дорога|0|дорога на север|loc.sd.4|лес на востоке|loc.sl.9|дорога на юг|loc.sd.2|лес на западе|loc.zl.11",
	"loc.sd.4"=>	"Северная дорога|0|дорога на юг|loc.sd.3|лес на западе|loc.zl.12",
	"loc.kzd"=>		"Дом у дороги|0|выйти на улицу|loc.sd.2",
	"loc.sl.1"=>	"Северный лес|0|дорога на севере|loc.sd.1|обогнуть озеро с севера|loc.sl.6|обогнуть озеро с юга|loc.sl.2",
	"loc.sl.2"=>	"Северный лес|0|вдоль берега на восток|loc.sl.3|лес на западе|loc.sl.1",
	"loc.sl.3"=>	"Северный лес|0|вдоль берега на запад|loc.sl.2|на восток|loc.sl.4",
	"loc.sl.4"=>	"Северный лес|0|на дорогу к воротам|loc.vd.1|на северо-восток|loc.vd.2|обогнуть озеро с севера|loc.sl.5|обогнуть озеро с юга|loc.sl.3",
	"loc.sl.5"=>	"Северный лес|0|на север вдоль забора|loc.sl.8|вдоль берега на запад|loc.sl.6|на юго-восток|loc.sl.4",
	"loc.sl.6"=>	"Северный лес|0|в лес на севере|loc.sl.7|вдоль берега на восток|loc.sl.5|на юго-запад|loc.sl.1",
	"loc.sl.7"=>	"Северный лес|0|к озеру|loc.sl.6|север|loc.sl.10|восток|loc.sl.8",
	"loc.sl.8"=>	"Северный лес|0|на юг к озеру|loc.sl.5|север|loc.sl.11|запад|loc.sl.7",
	"loc.sl.9"=>	"Северный лес|0|дорога на западе|loc.sd.3|на восток|loc.sl.10",
	"loc.sl.10"=>	"Северный лес|0|восток|loc.sl.11|юг|loc.sl.7|запад|loc.sl.9",
	"loc.sl.11"=>	"Северный лес|0|восток|loc.sl.12|юг|loc.sl.8|запад|loc.sl.10",
	"loc.sl.12"=>	"Северный лес|0|восток|loc.sl.14|запад|loc.sl.11",
	"loc.sl.14"=>	"Северный лес|0|восток|loc.sl.15|запад|loc.sl.12",
	"loc.sl.15"=>	"Северный лес|0|дорога на востоке|loc.vd.7|вдоль кладбища на юг|loc.sl.16|вдоль кладбища на запад|loc.sl.14",
	"loc.sl.16"=>	"Северный лес|0|дорога на востоке|loc.vd.6|на север|loc.sl.15|на юг|loc.sl.17",
	"loc.sl.17"=>	"Северный лес|0|дорога на востоке|loc.vd.5|дорога на юге|loc.vd.4|на север|loc.sl.16",
	"loc.vd.1"=>	"Восточная дорога|0|дорога на севере|loc.vd.2|лес на востоке|loc.vl.23|ворота на юге|loc.zvv|к озеру на западе|loc.sl.4",
	"loc.vd.2"=>	"Восточная дорога|0|войти в калитку|loc.kl.1|по дороге на восток|loc.vd.3|по дороге на юг|loc.vd.1|на запад|loc.sl.4|в лес на юге|loc.vl.23",
	"loc.vd.3"=>	"Восточная дорога|0|дорога на восток|loc.vd.4|лес на юге|loc.vl.24|дорога на запад|loc.vd.2",
	"loc.vd.4"=>	"Восточная дорога|0|север|loc.sl.17|дорога на восток|loc.vd.5|юг|loc.vl.25|дорога на запад|loc.vd.3",
	"loc.vd.5"=>	"Восточная дорога|0|дорога на север|loc.vd.6|восток|loc.vl.28|дорога на юг|loc.vd.4|запад|loc.sl.17",
	"loc.vd.6"=>	"Восточная дорога|0|дорога на север|loc.vd.7|восток|loc.vl.29|дорога на юг|loc.vd.5|запад|loc.sl.16",
	"loc.vd.7"=>	"Восточная дорога|0|восток|loc.vl.30|дорога на юг|loc.vd.6|запад|loc.sl.15",
	"loc.vl.1"=>	"Восточный лес|0|север|loc.vl.4|восток|loc.vl.2|запад|loc.bl.2",
	"loc.vl.2"=>	"Восточный лес|0|север|loc.vl.5|восток|loc.vl.3|запад|loc.vl.1",
	"loc.vl.3"=>	"Восточный лес|0|север|loc.vl.6|запад|loc.vl.2",
	"loc.vl.4"=>	"Восточный лес|0|север|loc.vl.7|восток|loc.vl.5|юг|loc.vl.1|запад|loc.bl.4",
	"loc.vl.5"=>	"Восточный лес|0|север|loc.vl.8|восток|loc.vl.6|юг|loc.vl.2|запад|loc.vl.4",
	"loc.vl.6"=>	"Восточный лес|0|север|loc.vl.9|юг|loc.vl.3|запад|loc.vl.5",
	"loc.vl.7"=>	"Восточный лес|0|север|loc.vl.10|восток|loc.vl.8|юг|loc.vl.4|перейти овраг на запад|loc.bl.6",
	"loc.vl.8"=>	"Восточный лес|0|север|loc.vl.11|восток|loc.vl.9|юг|loc.vl.5|запад|loc.vl.7",
	"loc.vl.9"=>	"Восточный лес|0|север|loc.vl.12|юг|loc.vl.6|запад|loc.vl.8",
	"loc.vl.10"=>	"Восточный лес|0|север|loc.vl.15|восток|loc.vl.11|юг|loc.vl.7|перейти овраг на запад|loc.bl.8",
	"loc.vl.11"=>	"Восточный лес|0|север|loc.vl.16|восток|loc.vl.12|юг|loc.vl.8|запад|loc.vl.10",
	"loc.vl.12"=>	"Восточный лес|0|север|loc.vl.17|юг|loc.vl.9|запад|loc.vl.11",
	"loc.vl.13"=>	"Восточный лес|0|север|loc.vl.18|восток|loc.vl.14|юг|loc.bl.7",
	"loc.vl.14"=>	"Восточный лес|0|север|loc.vl.19|восток|loc.vl.15|юг|loc.bl.8|запад|loc.vl.13",
	"loc.vl.15"=>	"Восточный лес|0|север|loc.vl.20|восток|loc.vl.16|юг|loc.vl.10|запад|loc.vl.14",
	"loc.vl.16"=>	"Восточный лес|0|север|loc.vl.21|восток|loc.vl.17|юг|loc.vl.11|запад|loc.vl.15",
	"loc.vl.17"=>	"Восточный лес|0|север|loc.vl.22|юг|loc.vl.12|запад|loc.vl.16",
	"loc.vl.18"=>	"Восточный лес|0|север|loc.vl.23|восток|loc.vl.19|юг|loc.vl.13|на дорогу|loc.zvv",
	"loc.vl.19"=>	"Восточный лес|0|север|loc.vl.24|восток|loc.vl.20|юг|loc.vl.14|запад|loc.vl.18",
	"loc.vl.20"=>	"Восточный лес|0|север|loc.vl.25|восток|loc.vl.21|юг|loc.vl.15|запад|loc.vl.19",
	"loc.vl.21"=>	"Восточный лес|0|север|loc.vl.26|восток|loc.vl.22|юг|loc.vl.16|запад|loc.vl.20",
	"loc.vl.22"=>	"Восточный лес|0|север|loc.vl.27|юг|loc.vl.17|запад|loc.vl.21",
	"loc.vl.23"=>	"Восточный лес|0|север|loc.vd.2|восток|loc.vl.24|юг|loc.vl.18|запад|loc.vd.1",
	"loc.vl.24"=>	"Восточный лес|0|север|loc.vd.3|восток|loc.vl.25|юг|loc.vl.19|запад|loc.vl.23",
	"loc.vl.25"=>	"Восточный лес|0|север|loc.vd.4|восток|loc.vl.26|юг|loc.vl.20|запад|loc.vl.24",
	"loc.vl.26"=>	"Восточный лес|0|север|loc.vl.28|восток|loc.vl.27|юг|loc.vl.21|запад|loc.vl.25",
	"loc.vl.27"=>	"Восточный лес|0|север|loc.vl.28|юг|loc.vl.22|запад|loc.vl.26",
	"loc.vl.28"=>	"Восточный лес|0|север|loc.vl.29|юг|loc.vl.26|юго-восток|loc.vl.27|запад|loc.vd.5",
	"loc.vl.29"=>	"Восточный лес|0|север|loc.vl.30|юг|loc.vl.28|запад|loc.vd.6",
	"loc.vl.30"=>	"Восточный лес|0|юг|loc.vl.29|на дорогу|loc.vd.7",
	"loc.zl.1"=>	"Западный лес|0|к северным воротам|loc.sd.1|север|loc.zl.10|запад|loc.zl.2",
	"loc.zl.2"=>	"Западный лес|0|север|loc.zl.9|восток|loc.zl.1|запад|loc.zl.3",
	"loc.zl.3"=>	"Западный лес|0|на тропу на юге|loc.zb.1|вдоль реки на запад|loc.zl.4|север|loc.zl.8|восток|loc.zl.2",
	"loc.zl.4"=>	"Западный лес|0|вдоль реки на восток|loc.zl.3|вдоль реки на запад|loc.zl.5|север|loc.zl.7",
	"loc.zl.5"=>	"Западный лес|0|вдоль реки на восток|loc.zl.4|на север|loc.zl.6",
	"loc.zl.6"=>	"Западный лес|0|север|loc.zl.15|восток|loc.zl.7|юг|loc.zl.5",
	"loc.zl.7"=>	"Западный лес|0|север|loc.zl.14|запад|loc.zl.6|юг|loc.zl.4|восток|loc.zl.8",
	"loc.zl.8"=>	"Западный лес|0|север|loc.zl.13|восток|loc.zl.9|юг|loc.zl.3|запад|loc.zl.7",
	"loc.zl.9"=>	"Западный лес|0|север|loc.zl.12|восток|loc.zl.10|юг|loc.zl.2|запад|loc.zl.8",
	"loc.zl.10"=>	"Западный лес|0|дорога на востоке|loc.sd.2|север|loc.zl.11|юг|loc.zl.1|запад|loc.zl.9",
	"loc.zl.11"=>	"Западный лес|0|дорога на востоке|loc.sd.3|на северо-запад|loc.zl.12|вдоль дороги на юг|loc.zl.10",
	"loc.zl.12"=>	"Западный лес|0|дорога на востоке|loc.sd.4|на юго-восток|loc.zl.11|юг|loc.zl.9|запад|loc.zl.13",
	"loc.zl.13"=>	"Западный лес|0|войти в дом|loc.krestd|восток|loc.zl.12|юг|loc.zl.8|запад|loc.zl.14",
	"loc.zl.14"=>	"Западный лес|0|восток|loc.zl.13|юг|loc.zl.7|запад|loc.zl.15",
	"loc.zl.15"=>	"Западный лес|0|восток|loc.zl.14|юг|loc.zl.6",
	"loc.krestd"=>	"Крестьянский дом|0|выйти на улицу|loc.zl.13",
	);

// игнорируем нефатальные ошибки
error_reporting (ERROR | WARNING);
function myErrorHandler ($errno, $errstr, $errfile, $errline) {}
set_error_handler("myErrorHandler");

if (!$debug) {
	header("Content-type:text/vnd.wap.wml;charset=utf-8"); 
	echo "<?xml version=\"1.0\"?>\n";
	echo "<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">"; 
	setlocale (LC_CTYPE, 'ru_RU.CP1251'); 
	function win2unicode ( $s ) { if ( (ord($s)>=192) & (ord($s)<=255) ) $hexvalue=dechex(ord($s)+848); if ($s=="Ё") $hexvalue="401"; if ($s=="ё") $hexvalue="451"; return("&#x0".$hexvalue.";");} 
	function translate($s) {return(preg_replace("/[А-яЁё]/e","win2unicode('\\0')",$s));} 
	}

if ($usesession) {
	ini_set('session.use_trans_sid','0');
	ini_set('url_rewriter.tags','');
	if ($sid) {session_id($sid);session_start(); $login = $_SESSION["login"];}	// надо вызывать перед стартом
	if (!$login && !$site) $tmp='';					// откроется основная страница, если не явно страница сайта
	} else {
		if (!$login) $login=$sid; else {if (substr($login,0,5)!='user.') $login='user.'.$login; $sid=$login;}	// FIX: временно
		$sid=$login."&p=$p";
		}
$login=str_replace('$','',$login);	// чтобы PHP не принимал за переменные
if ($sid) $sid.="&r=".rand(1,99);	// это чтобы каждая страница загружалась заново, а не бралась из кэша

if (file_exists($game_file)) {
	$file_save = fopen($game_file,"r+");
	if ($file_save===FALSE) {usleep(100000); $file_save = fopen($game_file,"r+");} // ждем по 100 миллисекунд, пока скрипт не прикроют по таймауту 
	flock($file_save,2);	// пока не закроем файл при сохранении или скрипт не завершится
	$game="";
	while (!feof ($file_save)) $game.= fgets($file_save, filesize($game_file));
	$game = unserialize($game);
	} else {$file_save = fopen($game_file,"w+");eval(implode('',file("f_blank.dat")));}

// html интерфейс
if ($gm_id && $gm==$gm_id) {
	if ($load_offline==1) eval(implode('',file("f_loadoffline.dat")));
	$sid.="&login=user.admin&gm=".$gm_id;
	$login="user.admin";
	eval(implode('',file("f_adminfull.dat")));
	savegame();
	die("");
	}

if ($site || $tmp=='') eval(implode('',file("f_site.dat"))); // все что касается сайта
if (!$login || !isset($game["players"][$login])) {$site="connect";eval(implode('',file("f_site.dat")));}

if (!$usesession) {
	$info=split("\|",$game["loc"][$game["players"][$login]][$login]["info"]);
	if ($info[0]!=$p) msg("Неправильный пароль<br/><a href=\"$PHP_SELF\">На главную</a><br/>",$game_title,0,'none');
	}
$player=&$game["loc"][$game["players"][$login]][$login];
$player["time"]=time();

// искусственный интелект
ai();

// подгружаемые модули
if ($macros) eval(implode('',file("f_macros.dat")));	// $macros обязательно первым!
if ($adm) eval(implode('',file("f_admin.dat")));
if ($look) eval(implode('',file("f_look.dat")));
if ($speak) eval(implode('',file("f_speak.dat")));
if ($say) eval(implode('',file("f_say.dat")));
if ($msg) eval(implode('',file("f_msg.dat")));
if ($attack) eval(implode('',file("f_attack.dat")));
if ($take) eval(implode('',file("f_take.dat")));
if ($drop) eval(implode('',file("f_drop.dat")));
if ($use) eval(implode('',file("f_use.dat")));		// $use обязательно раньше $list!
if ($list) {
	if ($list=='skill') eval(implode('',file("f_listskill.dat")));
	if ($list=='magic') eval(implode('',file("f_listmagic.dat")));
	if ($list=='inv') eval(implode('',file("f_listinv.dat")));
	if ($list=='all') eval(implode('',file("f_listall.dat")));
	}
if ($go) eval(implode('',file("f_go.dat")));


// собственно игра
//linkИгра

// новые сообщения
$count=0;
foreach($player["msg"] as $i) if ($i) $count++;
if ($count) $page_main.= "<p><a href=\"$PHP_SELF?sid=$sid&msg=1\">Нов.сообщ.:</a> $count"; 

// MAIN PAGE
if ($count) $page_main.= "\n<br/>"; else $page_main.= "<p>";
$page_main.= $player["life"]."/".$player["life_max"]." (".$player["mana"]."/".$player["mana_max"].")";
if ($player["ghost"]) $page_main.= "<br/>Вы призрак";
if ($player["crim"]) $page_main.= "<br/>Вы преступник";

// SOUNDS
$stmp="";
$loc=split("\|",$locations[$player["loc"]]);
for ($i=3;$i<count($loc);$i++) {
	if (substr($loc[$i],0,4)=='loc.') if (count($game["loc"][$loc[$i]])>0) foreach(array_keys($game["loc"][$loc[$i]]) as $j) if ((substr($j,0,5)=='user.') || substr($j,0,4)=='npc.') {if ($stmp=='') $stmp="\n<br/>Звуки: ".$loc[$i-1]; else $stmp.=", ".$loc[$i-1]; break;}
	};
$page_main.= $stmp;

// FIX: тут надо сортировать: нападающие, нпс, игроки, предметы
// Объекты
$stmp="";
$ind=0; $count=0; if(!$start) $start=0;
if ($game["loc"][$player["loc"]]) foreach (array_keys($game["loc"][$player["loc"]]) as $i) if ($i!=$login) {
	if ($ind>=$start && $ind<$start+$count_show) {	//FIX: может +1?
		// определим видимое название предметов и игроков/npc (включая кол-во и статус)
		if (substr($i,0,5)=='item.') {
			$k=split("\|",$game["loc"][$player["loc"]][$i]);
			if (substr($i,0,11)!='item.stand.' && $k[1]>1) $k=$k[0]." (".$k[1].")"; else $k=$k[0];
			} else {
				$k=$game["loc"][$player["loc"]][$i]["title"];
				if ($game["loc"][$player["loc"]][$i]["life_max"]>0) $ltmp=round($game["loc"][$player["loc"]][$i]["life"]*100/$game["loc"][$player["loc"]][$i]["life_max"]);
				$st='';
				if ($ltmp<100) $st.=$ltmp."%";
				if ($game["loc"][$player["loc"]][$i]["ghost"]) $st.=" призрак";
				if (substr($i,0,5)=='user.' && $game["loc"][$player["loc"]][$i]["crim"]) $st.=" преступник";
				$att=$game["loc"][$player["loc"]][$i]["attack"];
				if ($att && isset($game["loc"][$player["loc"]][$att]) && !$game["loc"][$player["loc"]][$att]["ghost"] && !$game["loc"][$player["loc"]][$i]["ghost"]) $st.=" атакует ".$game["loc"][$player["loc"]][$att]["title"];
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
$page_main.="\n<br/><a href=\"$PHP_SELF?sid=$sid&look=1\">Описание</a>";
if ($login==$admin) $page_main.="\n<br/><a href=\"$PHP_SELF?sid=$sid&adm=res\">res</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&adm=1\">admin</a>";

// карта меню
$page_main.="\n</p>\n</card>\n<card id=\"menu\" title=\"Меню\">\n<p>\n<br/><a href=\"$PHP_SELF?sid=$sid&attack=$(to)\">Атаковать</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&speak=$(to)\">Говорить</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&to=$(to)&list=inv\">Предмет</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&take=$(to)\">Взять</a>\n<br/><a href=\"$PHP_SELF?sid=$sid&look=$(to)\">Инфо</a>";

msg($page_main,$loc[0],1,'main');

// служебные функции

function savegame() {				// сохранение игры
	global $file_save,$game;
	if (isset($game["loc"]["loc.offline"])) {
		$file1 = fopen ("loc_offline.dat", "w");
		fputs($file1,serialize($game["loc"]["loc.offline"]));
		fclose ($file1);
		unset($game["loc"]["loc.offline"]);
		}
	rewind($file_save);	// в начало файла
	fputs($file_save,serialize($game));
	fflush($file_save);
	fclose($file_save);
	}

function getrandname() {			// генерирует случайное имя
	eval(implode('',file("f_getrandname.dat")));
	return $stmp;
	}

function addjournal($to,$msg) {		// добавляет в журнал и следит, чтоб не переполнился
	global $game,$journal_count;
	if (isset($game["players"][$to])) {
		$j=&$game["loc"][$game["players"][$to]][$to]["journal"];
		$j[]=$msg;
		if (count($j)>$journal_count) array_splice($j,0,count($j)-$journal_count);	// оставляем только n последних записей
		}
	}
function addjournalall($loc,$msg,$no1="",$no2="") {		// добавляет запись всем в журнал, кроме $no1 и $no2
	global $game;
	if ($game["loc"][$loc]) foreach (array_keys($game["loc"][$loc]) as $i) if ($i!=$no1 && $i!=$no2) if (isset($game["players"][$i])) addjournal($i,$msg);
	}

function msg($msg,$title='Амулет Дракона',$journal=1,$menu='') {//linkMsg		// вывод текста и выход
	// journal==1, то выведем карту с алертами
	// menu=='', кнопка "В игру" и "Назад"
	// menu=='none', без кнопок
	// menu=='main', основное меню
	global $game,$login,$page_size,$page_desc,$page_main,$debug,$PHP_SELF,$sid,$player,$page_size;

	if (!$debug) ob_start("translate");
	$wml = "\n<wml>";
	$wml.="\n<head>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"must-revalidate\"/>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"no-cache\"/>\n<meta forua=\"true\" http-equiv=\"Cache-Control\" content=\"no-store\"/>\n</head>";
	// ЖУРНАЛ
	if ($journal==1 && $player["journal"] && count($player["journal"])>0) {		// FIX: почему-то даже пустой массив имеет count=1
		$page_journal=implode("<br/>",$player["journal"]);
		$wml.= "\n<card title=\"Журнал\">\n<do type=\"accept\" label=\"Дальше\"><go href=\"#";
		if ($page_desc) $wml.= "desc";else $wml.= "main";
		$wml.= "\"/></do>\n<p>\n".$page_journal."\n</p>\n</card>";
		$player["journal"]=array();
		}

	$sizeok=1; 
	if ($player["look"]==$player["loc"]) {unset($player["look"]);$page_desc=0;}	// FIX: чтобы большое описание не вешало игрока
	if ($page_desc) {
		$player["look"]=$player["loc"];
		eval(implode('',file("f_desc.dat")));
		if (strlen($wml.$msg.$desc[$player["loc"]])>$page_size) $sizeok=0;
		$wml.= "\n<card id=\"desc\" title=\"".$title."\">\n<do type=\"accept\" label=\"Дальше\"><go href=\"";
		if ($sizeok) $wml.= "#main"; else $wml.= "$PHP_SELF?sid=$sid";
		$wml.= "\"/></do>\n<p>\n".$desc[$player["loc"]]."\n</p>\n</card>";
		}

	// ОСНОВНОЙ ЭКРАН
	if ($sizeok) {		// только если размер меньше или равен
	$wml.= "\n<card id=\"main\" title=\"".$title."\""; 
	if ($menu=='main') $wml.= " ontimer=\"$PHP_SELF?sid=$sid\"><timer value=\"600\"/";
	$wml.= ">";
	if ($menu=='') {
		$wml.= "\n<do name=\"o1\" type=\"options\" label=\"В игру\"><go href=\"$PHP_SELF?sid=$sid\"/></do>";
		$wml.= "\n<do name=\"a1\" type=\"accept\" label=\"Назад\"><prev/></do>";
		}
	if ($menu=='main') {
		$wml.= "\n<do name=\"o2\" type=\"options\" label=\"Предметы\"><go href=\"$PHP_SELF?sid=$sid&list=inv\"/></do>";
		$wml.= "\n<do name=\"o3\" type=\"options\" label=\"Магия\"><go href=\"$PHP_SELF?sid=$sid&list=magic\"/></do>";
		$wml.= "\n<do name=\"o5\" type=\"options\" label=\"Контакты\"><go href=\"$PHP_SELF?sid=$sid&msg=1\"/></do>";
		$j=6;
		for ($i=1;$i<10;$i++) if (isset($player["macros"][$i])) {$wml.= "\n<do name=\"o".$j."\" type=\"options\" label=\"".$player["macros"][$i]["name"]."\"><go href=\"$PHP_SELF?sid=$sid&macros=".$i."\"/></do>"; $j++;}
		$wml.= "\n<do name=\"o".$j."\" type=\"options\" label=\"[макросы]\"><go href=\"$PHP_SELF?sid=$sid&macros=list\"/></do>";
		}

	if (substr($msg,strlen($msg)-4)!="</p>") $msg.="\n</p>";
	if (substr($msg,0,2)!="<p") $msg="<p>\n".$msg;
	$wml.= "\n".$msg."\n</card>";
	};// if sizeok

	$wml.= "</wml>";
	$wml=str_replace("&amp;","&",$wml);		// чтобы привести к одному виду
	$wml=str_replace("&","&amp;",$wml);
	savegame();									// чтобы пока выводится клиенту, другие могли играть
	if ($debug) echo "\n<html><title>".$player["loc"]." : ".$title." : ".strlen($wml)."</title>";
	echo $wml;
	if ($debug) {
		echo "<p>Локация:<br>";
		if ($game["loc"][$player["loc"]]) foreach(array_keys($game["loc"][$player["loc"]]) as $i) if ($i!=$login) echo "<br/>".$i;
		echo "<p><a href=\"$PHP_SELF?sid=$sid&list=inv\">Инвентори:</a><br>";
		if ($player["items"]) foreach(array_keys($player["items"]) as $i) echo "<br/>".$i;
		echo "<p><a href=\"$PHP_SELF?sid=$sid&list=magic\">Магия</a>";
		}
	if (!$debug) ob_end_flush();
	die("");					// работа скрипта завершена
	}

function ai() {		// новый AI			//linkAI
	global $game,$locations,$login,$player,$time_logout;

	// раз в 12 часов обслуживание
	if (time()>$game["lastcopy"]+43200) eval(implode('',file("f_support.dat")));

	// проверим список онлайн и поудаляем кого долго не было
	if (time()>$game["lastai"]+60) {
	foreach(array_keys($game["players"]) as $j) if ($j!=$login) { 	// раз в минуту
		if (time()>$game["loc"][$game["players"][$j]][$j]["time"]+$time_logout) {
			if (isset($game["loc"][$game["players"][$j]][$j])) {
				// в оффлайн
				if (!isset($game["loc"]["loc.offline"])) eval(implode('',file("f_loadoffline.dat")));	// если нет, загрузим (один раз)
				$game["loc"]["loc.offline"][$j]=$game["loc"][$game["players"][$j]][$j];
				$game["loc"]["loc.offline"][$j]["journal"]=array();
				$game["loc"]["loc.offline"][$j]["loc"]=$game["players"][$j];
				unset($game["loc"][$game["players"][$j]][$j]);
				addjournalall($game["players"][$j],$game["loc"]["loc.offline"][$j]["title"]." исчез",$j);
				unset($game["players"][$j]);
				} else unset($game["players"][$j]);
			}
		}
	$game["lastai"]=time();
	}

	if (!$login || !$player) return;	// это когда только смотрят список онлайн

	// проверяем только текущую и соседние локации
	doai($player["loc"]);
	$ok=array($player["loc"]=>1);	// эту проверили
	$loc=split("\|",$locations[$player["loc"]]);
	for ($i=3;$i<count($loc);$i++) if (substr($loc[$i],0,4)=='loc.') {
		doai($loc[$i]);
		$ok[$loc[$i]]=1;
		$loc1=split("\|",$locations[$loc[$i]]);
		for ($j=3;$j<count($loc1);$j++) if (substr($loc1[$j],0,4)=='loc.') if (!isset($ok[$loc1[$j]])) {doai($loc1[$j]); $ok[$loc1[$j]]=1;}
		}
	}

function doai($i) {				// искусственный интеллект, проверяем локацию с именем $i
	global $game,$locations,$time_logout,$time_regenerate,$time_objects_destroy,$time_crim;

	$loc=split("\|",$locations[$i]);

	// таймеры локации
	if (isset($game["loc_del"][$i])) foreach (array_keys($game["loc_del"][$i]) as $j) {
		if (time()>$game["loc_del"][$i][$j]) {	// удаление предмета/npc
			if (substr($j,0,4)=='npc.') addjournalall($i,$game["loc"][$i][$j]["title"]." исчез");
			unset($game["loc"][$i][$j]);
			unset($game["loc_del"][$i][$j]);
			if (count($game["loc_del"][$i])==0) unset($game["loc_del"][$i]);
			}
		}
	if (isset($game["loc_add"][$i])) foreach (array_keys($game["loc_add"][$i]) as $j) {
		if (time()>$game["loc_add"][$i][$j]["time"]) {	// добавление предмета/npc
			if ($game["loc_add"][$i][$j]["respawn"]) {
				$respawn=split("\|",$game["loc_add"][$i][$j]["respawn"]);
				$game["loc_add"][$i][$j]["time"]=time()+rand($respawn[0],$respawn[1]);
				if ($respawn[2] && $respawn[3] && substr($j,0,5)=='item.') {	// обновим кол-во
					$item=split("\|",$game["loc_add"][$i][$j]["item"]);
					$item[1]=rand($respawn[2],$respawn[3]);
					$game["loc_add"][$i][$j]["item"]=implode("|",$item);
					}
				}
			$game["loc"][$i][$j]=$game["loc_add"][$i][$j]["item"];
			if (substr($j,0,4)=='npc.') {
				addjournalall($i,"Появился ".$game["loc_add"][$i][$j]["item"]["title"]);
				unset($game["loc_add"][$i][$j]);	// npc удаляем, для предметов только обновляем время
				if (count($game["loc_add"][$i])==0) unset($game["loc_add"][$i]); 
				}
			}
		}

	// удалим стражу, если вышло время
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (substr($j,0,9)=='npc.guard') if (time()>$game["loc"][$i][$j]["delete"]) {unset($game["loc"][$i][$j]); addjournalall($i,$game["loc"][$i][$j]["title"]." исчез");}

	// есть ли лекарь, есть ли гарды, список кримов (монстры и крим игроки) и список игроков
	$crim=array();
	$users=array();
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (substr($j,0,5)=='user.' || substr($j,0,4)=='npc.') {
		if ($game["loc"][$i][$j]["healer"]) $healer=$game["loc"][$i][$j]["title"];
		if (substr($j,0,9)=='npc.crim.' || $game["loc"][$i][$j]["crim"]) if (!$game["loc"][$i][$j]["ghost"]) $crim[]=$j;	// кримов-призраков не добавляем
		if (substr($j,0,9)=="npc.guard") $guard=1;
		if (substr($j,0,5)=="user." && !$game["loc"][$i][$j]["ghost"]) $users[]=$j;
		}
	// добавляем стражу от 1 до 3 гардов
	if ($loc[1] && count($crim)>0 && !$guard) for ($k=0;$k<rand(1,3);$k++) {	
		srand ((float) microtime() * 10000000);
		$id = "npc.guard.".rand(5,9999);
		$title = getrandname()." [стража]";
		$game["loc"][$i][$id]=array("title"=>$title,"life"=>"1000","life_max"=>"1000","speak"=>"npc.guard","war"=>"100|100|100|2|0|10|20|0|0|10|30|40|алебардой|0||","delete"=>time()+$time_logout);
		//$game["loc_del"][$i][$id]=time()+$time_logout;	// когда удалить стражу
		addjournalall($i,"Появился ".$title);
		}

	// теперь обработаем игроков и npc
	if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $j) if (isset($game["loc"][$i][$j]) && (substr($j,0,5)=='user.' || substr($j,0,4)=='npc.')) {
		// восстановим жизнь и ману согласно прошедшему времени
		$tm=time()-$game["loc"][$i][$j]["time_regenerate"];
		if ($tm>$time_regenerate && !$game["loc"][$i][$j]["ghost"]) {
			$life=0; $mana=0;
			if (substr($j,0,5)=='user.') {	// проверим скиллы регенерация и медитация
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

		// игроки
		if (substr($j,0,5)=="user.") {
			// проверим, не прошло ли время крима
			if (time()>$game["loc"][$i][$j]["time_crim"]) {unset($game["loc"][$i][$j]["crim"]); unset($game["loc"][$i][$j]["time_crim"]);}
			// если есть лекарь, то воскресимся...
			if ($game["loc"][$i][$j]["ghost"] && $healer) {addjournalall($i,$healer.": Возвращайся к живым, ".$game["loc"][$i][$j]["title"]."!");ressurect($j);}
			}

		// NPC
		if (substr($j,0,4)=='npc.') {
			$b=0;	// надо ли continue, если ушли в др. локацию
			// первым делом следуем за хозяином
			$owner=$game["loc"][$i][$j]["owner"];
			$follow=$game["loc"][$i][$j]["follow"];
			$guard=$game["loc"][$i][$j]["guard"];
			$attack=$game["loc"][$i][$j]["attack"];
			if ($owner) {
				// хозяин крима тоже крим
				if ($game["loc"][$i][$j]["crim"] && isset($game["loc"][$i][$owner])) docrim($owner);
				// если вышло время служения
				if (time()>$game["loc"][$i][$j]["time_owner"]) {
					addjournal($owner,$game["loc"][$i][$j]["title"]." покинул вас");
					if ($game["loc"][$i][$j]["destroyonfree"]) {addjournalall($i,$game["loc"][$i][$j]["title"]." исчез"); unset($game["loc"][$i][$j]); continue;}	// дальше не обрабатываем его 
						else {unset($game["loc"][$i][$j]["time_owner"]); unset($game["loc"][$i][$j]["owner"]);unset($game["loc"][$i][$j]["follow"]); unset($game["loc"][$i][$j]["guard"]);}
					}
				}
			if ($follow && !isset($game["loc"][$i][$follow])) for ($k=3;$k<count($loc);$k++) if (substr($loc[$k],0,4)=='loc.' && isset($game["loc"][$loc[$k]][$follow])) {
				// нашли в соседней локации $follow, идем туда
				$game["loc"][$loc[$k]][$j] = $game["loc"][$i][$j];
				unset($game["loc"][$i][$j]);
				unset($game["loc"][$k][$j]["attack"]);
				addjournalall($i,$game["loc"][$loc[$k]][$j]["title"]." ушел ".$loc[$k-1]);
				addjournalall($loc[$k],"Пришел ".$game["loc"][$loc[$k]][$j]["title"]);
				$b=1;	// больше не обрабатывать в текущей локации
				break;
				}
			if ($b) continue;		//$j ушел из этой локации

			// пытаемся преследовать (если ни за кем не следуем)
			if ($attack && !$game["loc"][$i][$j]["follow"] && !isset($game["loc"][$i][$attack])) for ($k=3;$k<count($loc);$k++) if (substr($loc[$k],0,4)=='loc.' && isset($game["loc"][$loc[$k]][$attack])) {	// нашли!
				// хорошие не будут преследовать в неохраняему зону, а плохие не сунутся в гард зону, а гарды всегда преследуют!
				$crimj=$game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.';
				$loc1=split("\|",$locations[$loc[$k]]);
				$b=0;	
				if (($crimj && !$loc1[1]) || (!$crimj && $loc1[1]) || substr($j["id"],0,9)=="npc.guard") $b=1;	// продолжить погоню
				// проверим скилл игрока skill.hiding, может спрятался (от гардов не действует)
				if (substr($attack,0,5)=='user.' && !substr($j,0,9)=="npc.guard") {
					$skills=split("\|",$game["loc"][$loc[$k]][$attack]);
					if (rand(0,100)<=($skills[17]+$skills[1])*10) {$b=0;addjournal($attack,"Вы скрылись от погони!");}
					}

				if ($b) {	// погоня!
					$game["loc"][$loc[$k]][$j] = $game["loc"][$i][$j];
					unset($game["loc"][$i][$j]);
					addjournalall($i,$game["loc"][$loc[$k]][$j]["title"]." ушел ".$loc[$k-1]);
					addjournalall($loc[$k],"Пришел ".$game["loc"][$loc[$k]][$j]["title"]);
					} else unset($game["loc"][$i][$j]["attack"]);
				break;
				}
			if ($b) continue;		//$j ушел из этой локации
			// если на того, кого охраняем guard=id кто-то нападает, атакуем его
			if ($guard && isset($game["loc"][$i][$guard])) {
				if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $k) if ($game["loc"][$i][$k]["attack"]==$guard) {$game["loc"][$i][$j]["attack"]=$k; break;}
				}

			// гарды атакуют кримов, кримы игроков
			if (!$game["loc"][$i][$j]["attack"]) {
				if (substr($j,0,9)=="npc.guard" && count($crim)>0) $game["loc"][$i][$j]["attack"]=$crim[rand(0,count($crim)-1)];
				if (($game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.') && count($users)>0) {
					$b=0;
					$attack=$users[rand(0,count($users)-1)];
					if (substr($attack,0,5)=='user.') {$skills=split("\|",$game["loc"][$i][$attack]); if (rand(0,100)<=$skills[1]*5) {$b=1;addjournal($attack,"Вы укрылись от атаки ".$game["loc"][$i][$j]["title"]);}}
					if (!$b) $game["loc"][$i][$j]["attack"]=$attack;
					}

				// если все еще не атакуем, то атакуем того, кто атакует нас
				//if (!$game["loc"][$i][$j]["attack"]) if ($game["loc"][$i]) foreach (array_keys($game["loc"][$i]) as $k) if ($game["loc"][$i][$k]["attack"]==$j) {$game["loc"][$i][$j]["attack"]=$k; break;}
				}

			// проверяем случайное движение NPC
			if (!$game["loc"][$i][$j]["attack"] && $game["loc"][$i][$j]["move"]) {
				$move=split("\|",$game["loc"][$i][$j]["move"]);
				$b=0;
				if (time()>$game["loc"][$i][$j]["time_nextmove"]) {	// идем...
					$k=$loc[2+2*rand(0,(count($loc)-2)/2-1)+1];	// случайный выход
					// плохие не идут в гард зону, а хорошие из нее
					$crimj=$game["loc"][$i][$j]["crim"] || substr($j,0,9)=='npc.crim.';
					$loc1=split("\|",$locations[$loc[$k]]);
					if (($crimj && !$loc1[1]) || (!$crimj && $loc1[1])) $b=1;	// идти
					if ($k==$i) $b=0;
					if ($b) {
						// переход
						$game["loc"][$k][$j]=$game["loc"][$i][$j];
						unset($game["loc"][$i][$j]);
						addjournalall($k,"Пришел ".$game["loc"][$k][$j]["title"]);
						$s=$game["loc"][$k][$j]["title"]." ушел ";
						if (array_search($k,$loc)) $s.=$loc[array_search($k,$loc)-1];
						addjournalall($i,$s);
						$game["loc"][$k][$j]["time_nextmove"]=time()+rand($move[1],$move[2]);	// след. ход
						}
					}
				}
			if ($b) continue;		//$j ушел из этой локации
			// проверяем атаку NPC
			if ($game["loc"][$i][$j]["attack"] && $game["loc"][$i][$game["loc"][$i][$j]["attack"]]["ghost"]) unset($game["loc"][$i][$j]["attack"]);
			if ($game["loc"][$i][$j]["attack"]) attack($i,$j,$game["loc"][$i][$j]["attack"]);
			}//npc		
		}//foreach user & npc

	// удалим пустой массив
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

function attack($loc,$fromid,$toid,$magic='',$answer=1) {//linkAttack		// answer=1 - обороняющийся отвечает, 0 -нет
	global $attackf;
	global $game,$locations,$login,$time_crim,$points_levelup,$time_objects_destroy,$time_logout,$time_defspeed;
	if (!$attackf) $attackf=implode('',file("f_attackf.dat"));
	eval($attackf);
	}

function view($file) {eval(implode('',file("f_view.dat")));}