<?php

/**
 * Class de gestion des dates
 */
class Date {

    var $year;
    var $month;
    var $day;
    var $hour;
    var $min;
    var $sec;
    private $lang;
    public $fr_month = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'decembre'];
    public $en_month = ['', 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
    public $pt_month = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
    public $es_month = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    public $fr_day = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
    public $en_day = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    public $pt_day = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
    public $es_day = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];

    public function Date($year = NULL, $month = NULL, $day = NULL, $hour = 0, $min = 0, $sec = 0) {
        static $nbinstance;

        if (!$nbinstance)
            Date::initStaticVar();
        $nbinstance++;
        if (func_num_args() == 0)
            $this->setDateFromTimeStamp($_SERVER['REQUEST_TIME']);
        elseif (func_num_args() == 3 || func_num_args() == 6) {
            $this->year = $year;
            $this->month = $month;
            $this->day = $day;
            $this->hour = $hour;
            $this->min = $min;
            $this->sec = $sec;
        } else
            trigger_error("nombre d'argument invalide");
    }

    public function setDateFromPattern($date, $pattern) {
        if (!Date::isValidDateByPattern($date, $pattern))
            return FALSE;
        $tabdate = Date::extractDateFromPattern($date, $pattern);
        $this->day = $tabdate[2];
        $this->month = $tabdate[1];
        $this->year = $tabdate[0];
        $this->hour = $tabdate[3];
        $this->min = $tabdate[4];
        $this->sec = $tabdate[5];
        $this->majDate();
    }

    public function setDateFromTimeStamp($timestamp) {
        $date = getdate($timestamp);
        $this->year = $date['year'];
        $this->month = $date['mon'];
        $this->day = $date['mday'];
        $this->hour = $date['hours'];
        $this->min = $date['minutes'];
        $this->sec = $date['seconds'];
    }

    public function setDay($jour) {
        $this->day = $jour;
        $this->majDate();
    }

    public function setMonth($mois) {
        $this->month = $mois;
        $this->majDate();
    }

    public function setYear($anne) {
        $this->year = $anne;
        $this->majDate();
    }

    public function setHour($hour) {
        $this->hour = $hour;
        $this->majDate();
    }

    public function setMin($min) {
        $this->min = $min;
        $this->majDate();
    }

    public function setSec($sec) {
        $this->sec = $sec;
        $this->majDate();
    }

    /**
     * Permet d'afficher une date selon de le format voulu
     * taper j entre % poour afficher le nom du jour par exemple %j%
     * J pour la date (entre 1 et 31)
     * M pour le nom du mois
     * m pour le numéro du mois (de 1 à 12)
     * h pour l'heure
     * mn pour les minutes
     * s pour les secondes
     * ns pour le numero de la semaine
     * nja pour la position de la jounée dans l'année (de 1 à 365)
     * ju pour la date julienne
     * Y pour l'année sur 4chiffres
     * y pour l'année sur 2chiffres
     * njs pour le numero du jour de la semaine de (de 1 à 7)
     * stamp pour le timestamp
     * j3 pour afficher le nom du jour sur 3 lettres
     * @param <string> $pattern Format de l'affichage
     * @return <string> Date formatée
     */
    public function str($pattern = '%J%/%M%/%Y%') {
        $in = [
            '/%j%/',
            '/%J%/',
            '/%m%/',
            '/%M%/',
            '/%h%/',
            '/%mn%/',
            '/%s%/',
            '/%ns%/',
            '/%nja%/',
            '/%ju%/',
            '/%Y%/',
            '/%y%/',
            '/%njs%/',
            '/%stamp%/',
            '/%j3%/',
            '/%m3%/'
        ];
        $out = [
            $this->dayWord(),
            str_pad($this->day, 2, 0, STR_PAD_LEFT),
            $this->monthWord(),
            str_pad($this->month, 2, 0, STR_PAD_LEFT),
            str_pad($this->hour, 2, 0, STR_PAD_LEFT),
            str_pad($this->min, 2, 0, STR_PAD_LEFT),
            str_pad($this->sec, 2, 0, STR_PAD_LEFT),
            $this->getNumSemaine(),
            $this->getNumJourAnne(),
            $this->getNumJourJulien(),
            $this->year,
            substr($this->year, 2, 2),
            $this->getNumJourSemaine(),
            $this->getStamp(),
            substr($this->dayWord(), 0, 3),
            substr($this->monthWord(), 0, 3)
        ];

        return preg_replace($in, $out, $pattern);
    }

    public function getNumJourAnne() {
        return Date::getDayOfYear($this->year, $this->month, $this->day);
    }

    public function getNumSemaine() {
        return Date::getWeekNumber($this->getNumJourJulien());
    }

    public function getNumJourSemaine() {
        return ($this->getNumJourJulien() + 1.5) % 7;
    }

    public function getNumJourJulien() {
        $hours = 12;
        $ggg = 1;
        $jd = -1 * floor(7 * (floor(($this->month + 9) / 12) + $this->year) / 4);
        $s = 1;
        if (($this->month - 9) < 0)
            $s = -1;
        $a = abs($this->month - 9);
        $j1 = floor($this->year + $s * floor($a / 7));
        $j1 = -1 * floor((floor($j1 / 100) + 1) * 3 / 4);
        $jd = $jd + floor(275 * $this->month / 9) + $this->day + ($ggg * $j1);
        $jd = floor($jd + 1721027 + 2 * $ggg + 367 * $this->year - 0.5);
        $jd = $jd + ($hours / 24);
        if ($jd < 0)
            $jd = 0;

        return round($jd);
    }

    public function getStamp() {
        return @mktime($this->hour, $this->min, $this->sec, $this->month, $this->day, $this->year);
    }

    public function isBissextile() {
        return Date::yearIsBissextile($this->year);
    }

    public function majDate() {

        $time = Date::getSecondsFromTime($this->sec, $this->min, $this->hour);
        if ($time >= 86400) {
            $tbTime = Date::getTimeFromSeconds($time);
            $this->day = $this->day + $tbTime['day'];
            $ntime = Date::getSecondsFromTime($tbTime['sec'], $tbTime['min'], $tbTime['hou']);
            $tbTime = Date::getTimeFromSeconds($ntime);
        } elseif ($time < 0) {
            $tbTime = Date::getTimeFromSeconds(abs($time));
            if ($time % 86400 != 0)
                $r = 1;
            $this->day = $this->day - ($tbTime['day'] + $r );
            $ntime = Date::getSecondsFromTime($tbTime['sec'], $tbTime['min'], $tbTime['hou']);
            $tbTime = Date::getTimeFromSeconds(86400 - $ntime);
        }
        else {
            $ntime = Date::getSecondsFromTime($this->sec, $this->min, $this->hour);
            $tbTime = Date::getTimeFromSeconds($ntime);
        }
        $this->hour = $tbDate['hour'] = $tbTime['hou'];
        $this->min = $tbDate['minute'] = $tbTime['min'];
        $this->sec = $tbDate['seconds'] = $tbTime['sec'];

        if ($this->month <= 0 || $this->month > 12) {
            $this->month = $this->month - 1;
            $mm = $this->month % 12;
            if ($mm < 0)
                $mm = 12 - abs($mm);
            $this->year = $this->year + floor($this->month / 12);
            $this->month = $mm + 1;
        }
        $julian = $this->getNumJourJulien();
        $newdate = & Date::getDateFromJulian($julian);
        $this->day = $newdate->day;
        $this->month = $newdate->month;
        $this->year = $newdate->year;
    }

    public function nextDay() {
        $this->day++;
        $this->majDate();
    }

    public function precDay() {
        $this->day--;
        $this->majDate();
    }

    public function nextWeek() {
        $this->day += 7;
        $this->majDate();
    }

    public function precWeek() {
        $this->day -= 7;
        $this->majDate();
    }

    public function nextMonth() {
        $this->month++;
        $this->majDate();
    }

    public function precMonth() {
        $this->month--;
        $this->majDate();
    }

    public function addtime($year, $month, $day, $hour, $min, $sec) {
        $this->year += $year;
        $this->month += $month;
        $this->day += $day;
        $this->hour += $hour;
        $this->min += $min;
        $this->sec += $sec;
        $this->majDate();
    }

    public function getThisWeek() {
        return Date::getWeek($this->year, $this->getNumSemaine());
    }

    public function getThisMonth() {
        return Date::getMonth($this->year, $this->month);
    }

    public function &getFirstDayofWeek() {
        $day = $this->getNumJourJulien() + 7 * ($this->getNumJourSemaine() - 1);
        $date = &Date::getDateFromJulian($day);
        $offset = $day - $date->getNumJourSemaine() + 1;

        return Date::getDateFromJulian($offset);
    }

    public function getLastDayofWeek() {
        $day = $this->getNumJourJulien() + (7 - $this->getNumJourSemaine() );

        return Date::getDateFromJulian($day);
    }

    public function getFirstDayOfMonth() {
        $copie = $this;
        $copie->day = 1;
        $copie->majDate();

        return $copie;
    }

    public function getLastDayOfMonth() {
        $copie = $this;
        $copie->day = 0;
        $copie->month++;
        $copie->majDate();
        return $copie;
    }

    public function compareTo(&$date, $comp_hour = true) {
        return Date::compare($this, $date, $comp_hour);
    }

    public function getDiffTo(&$date) {
        if ($this->compareTo($date) == -1) {
            $small = & $this;
            $great = & $date;
        } else {
            $small = & $date;
            $great = & $this;
        }
        $diff['day'] = $great->getNumJourJulien() - $small->getNumJourJulien();
        $diff['tsec'] = $great->getSeconds() - $small->getSeconds();
        if ($diff['tsec'] < 0) {
            $diff['day'] = $diff['day'] - 1;
            $diff['tsec'] = 86400 - abs($diff['tsec']);
        }
        $time = Date::getTimeFromSeconds($diff['tsec']);
        $diff['hou'] = $time['hou'];
        $diff['min'] = $time['min'];
        $diff['sec'] = $time['sec'];
        return $diff;
    }

    public function getFerieeOfThisYear() {
        return Date::getFerieeOfYear($this->year);
    }

    public function estFeriee() {
        foreach ($this->getFerieeOfThisYear() as $date) {
            if ($this == $date)
                return TRUE;
        }
        return FALSE;
    }

    public function dayWord() {
        return $this->{$this->lang . '_day'}[$this->getNumJourSemaine()];
    }

    public function monthWord() {
        return $this->{$this->lang . '_month'}[$this->month];
    }

    public static function staticVar($_nom, $val = NULL) {
        static $tabvar;
        if (isset($val)) {
            $tabvar[$_nom] = $val;
        }
        $return = $tabvar[$_nom];
        return $return;
    }

    public static function initStaticVar() {
        DATE::staticVar('MYSQL_DATETIME', "%Y%-%M%-%J% %h%:%mn%:%s%");
        DATE::staticVar('MYSQL_TIMESTAMP', "%Y%%M%%J%%h%%mn%%s%");
    }

    public function setLanguage($lang = 'fr') {
        $this->lang = $lang;
    }

    public function &getDateFromPattern($date, $pattern) {
        $tabdate = & Date::extractDateFromPattern($date, $pattern);
        if (!$tabdate)
            return FALSE;
        $d = new Date($tabdate[0], $tabdate[1], $tabdate[2], $tabdate[3], $tabdate[4], $tabdate[5]);
        $d->majDate();
        return $d;
    }

    public function getWeek($year, $week) {
        $date = new Date($year, 1, 1, 0, 0, 0);
        $day = $date->getNumJourJulien() + 7 * ($week);
        $date = Date::getDateFromJulian($day);
        $offset = $day - $date->getNumJourSemaine() + 1;
        for ($i = $offset; $i <= $offset + 6; $i++) {
            $tbWeek[] = Date::getDateFromJulian($i);
        }
        return $tbWeek;
    }

    public function getMounth($year, $mounth) {
        $day = Date::getJulianDay($year, $mounth, 1);
        $nb = getNbDayOfMounth($year, $mounth);
        for ($i = $day; $i <= $day + $nb - 1; $i++) {
            $tbMounth[] = Date::getDateFromJulian($i);
        }
        return $tbMounth;
    }

    public function yearIsBissextile($year) {
        if ($year % 4 > 0)
            return FALSE;
        elseif ($year % 100 > 0)
            return TRUE;
        elseif ($year % 400 > 0)
            return FALSE;
        else
            return TRUE;
    }

    public function isValidDate($year, $month, $day, $hour = 0, $min = 0, $sec = 0) {
        $return = NULL;
        $julian = Date::getJulianDay($year, $month, $day);
        $date = & Date::getDateFromJulian($julian);
        if ($date->year == $year && $date->month == $month && $date->day == $day)
            $return = TRUE;
        else
            $return = FALSE;
        if (func_num_args() == 6) {
            if ($hour >= 0 && $hour <= 23 && $min >= 0 && $min <= 59 && $sec >= 0 && $sec <= 59)
                $return = $return && TRUE;
            else
                $return = FALSE;
        }
        elseif (func_num_args() > 3)
            trigger_error("Nombre de paramètre incohérent");
        return $return;
    }

    public function isValidDateByPattern($date, $pattern) {
        $tabdate = & Date::extractDateFromPattern($date, $pattern);
        if (!$tabdate)
            return FALSE;
        return Date::isValidDate($tabdate[0], $tabdate[1], $tabdate[2], $tabdate[3], $tabdate[4], $tabdate[5]);
    }

    public function compare(&$a, &$b, $comp_hour = true) {
        $a_j = $a->getNumJourJulien();
        $b_j = $b->getNumJourJulien();
        if ($a_j < $b_j)
            return -1;
        if ($a_j > $b_j)
            return 1;
        if ($a_j == $b_j && $comp_hour) {
            $a_s = $a->getSeconds();
            $b_s = $b->getSeconds();
            if ($a_s < $b_s)
                return -1;
            if ($a_s > $b_s)
                return 1;
            if ($a_s == $b_s)
                return 0;
        }
    }

    public function asortDate(&$tab) {
        uasort($tab, ["Date", "compare"]);
    }

    public function arsortDate(&$tab) {
        uasort($tab, ["Date", "compare"]);
        $tab = array_reverse($tab, TRUE);
    }

    public function getFerieeOfYear($year) {
        $return = [];

        $return[] = new Date($year, 1, 1);

        $G = $year % 19;
        $C = floor($year / 100);
        $H = ($C - floor($C / 4) - floor((8 * $C + 13) / 25) + (19 * $G) + 15) % 30;
        $I = $H - floor($H / 28) * (1 - floor($H / 28) * floor(29 / ($H + 1)) * floor((21 - $G) / 11));
        $J = ($year + floor($year / 4) + $I + 2 - $C + floor($C / 4)) % 7;
        $L = $I - $J;
        $M = 3 + floor(($L + 40) / 44);
        $J = $L + 28 - 31 * floor($M / 4);
        $p = new Date($year, $M, $J + 1);
        $p->majDate();
        $return[] = $p;

        $p = new Date($year, $M, $J + 39);
        $p->majDate();
        $return[] = & $p;

        $return[] = new Date($year, 5, 1);
        $return[] = new Date($year, 5, 8);
        $return[] = new Date($year, 7, 14);
        $return[] = new Date($year, 8, 15);
        $return[] = new Date($year, 11, 1);
        $return[] = new Date($year, 11, 11);
        $return[] = new Date($year, 12, 25);
        return $return;
    }

    private function getSeconds() {
        return Date::getSecondsFromTime($this->sec, $this->min, $this->hour);
    }

    private function extractDateFromPattern($date, $pattern) {
        if ($pattern2 = preg_replace('/%J%/', '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';

            if (preg_match($pattern2, $date, $match)) {
                $day = $match[1];
            } else
                return FALSE;
        } else
            return FALSE;
        if ($pattern2 = preg_replace("/%M%/", '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';
            if (preg_match($pattern2, $date, $match)) {
                $month = $match[1];
            } else
                return FALSE;
        } else
            return FALSE;
        if ($pattern2 = preg_replace("/%Y%/", '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';
            if (preg_match($pattern2, $date, $match)) {
                $year = $match[1];
            } else
                return FALSE;
        } else
            return FALSE;

        if ($pattern2 = preg_replace("/%h%/", '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';
            if (preg_match($pattern2, $date, $match)) {
                $hour = $match[1];
            } else
                return FALSE;
        } else
            $hour = 0;
        if ($pattern2 = preg_replace("/%mn%/", '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';
            if (preg_match($pattern2, $date, $match)) {
                $minute = $match[1];
            } else
                return FALSE;
        } else
            $minute = 0;
        if ($pattern2 = preg_replace("/%s%/", '(\d+)', $pattern)) {
            Date::extractDateFromPattern2($pattern2);
            $pattern2 = '/' . addcslashes($pattern2, '/') . '/';
            if (preg_match($pattern2, $date, $match)) {
                $seconds = $match[1];
            } else
                return FALSE;
        } else
            $seconds = 0;

        return [$year, $month, $day, $hour, $minute, $seconds];
    }

    private function extractDateFromPattern2(&$str) {
        $str = preg_replace('/%\w+%/', '.+', $str);
    }

    private function getJulianDay($year, $month, $day) {
        $hours = 12;
        $ggg = 1;

        $jd = -1 * floor(7 * (floor(($month + 9) / 12) + $year) / 4);
        $s = 1;
        if (($month - 9) < 0)
            $s = -1;
        $a = abs($month - 9);
        $j1 = floor($year + $s * floor($a / 7));
        $j1 = -1 * floor((floor($j1 / 100) + 1) * 3 / 4);
        $jd = $jd + floor(275 * $month / 9) + $day + ($ggg * $j1);
        $jd = floor($jd + 1721027 + 2 * $ggg + 367 * $year - 0.5);
        $jd = $jd + ($hours / 24);
        if ($jd < 0)
            $jd = 0;
        return round($jd);
    }

    private function getDateFromJulian($julian) {
        $a = $julian + 32044;
        $b = floor((4 * $a + 3) / 146097);
        $c = $a - floor(($b * 146097) / 4);

        $d = floor((4 * $c + 3) / 1461);
        $e = $c - floor((1461 * $d) / 4);
        $m = floor((5 * $e + 2) / 153);
        $day = $e - floor((153 * $m + 2) / 5) + 1;
        $month = $m + 3 - 12 * floor($m / 10);
        $year = $b * 100 + $d - 4800 + floor($m / 10);
        return new Date((int) $year, (int) $month, (int) $day, 0, 0, 0);
    }

    private function getWeekNumber($julian) {
        $d4 = ($julian + 31741 - ($julian % 7)) % 146097 % 36524 % 1461;
        $l = floor($d4 / 1460);
        $d1 = (($d4 - $l) % 365) + $l;
        $nb = floor($d1 / 7) + 1;
        return (int) $nb;
    }

    private function getDayOfYear($year, $month, $day) {
        if (Date::isBissextile($year)) {
            $n = floor(275 * $month / 9) - floor(($month + 9) / 12) + $day - 30;
        } else {
            $n = floor(275 * $month / 9) - 2 * floor(($month + 9) / 12) + $day - 30;
        }
        return $n;
    }

    private function getSecondsFromTime($seconds = 0, $minute = 0, $hour = 0, $day = 0) {
        return (86400 * $day) + (3600 * $hour) + (60 * $minute) + $seconds;
    }

    private function getTimeFromSeconds($sec) {
        $tbTime['sec'] = $sec % 60;
        $tbTime['min'] = floor($sec / 60) % 60;
        $tbTime['hou'] = floor($sec / 3600) % 24;
        $tbTime['day'] = floor($sec / 86400);
        return $tbTime;
    }

}
