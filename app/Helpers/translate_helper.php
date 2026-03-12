<?php

function t($key, $default = '')
{
    $locale = session()->get('site_lang') ?? 'en';

    $file = APPPATH . "Language/{$locale}/site.php";
    $enFile = APPPATH . "Language/en/site.php";

    if (!file_exists(dirname($file))) {
        mkdir(dirname($file), 0755, true);
    }

    $en = file_exists($enFile) ? include $enFile : [];
    $translations = file_exists($file) ? include $file : [];

    if (isset($translations[$key])) {
        return $translations[$key];
    }

    if (!isset($en[$key])) {
        $en[$key] = $default ?: $key;
        file_put_contents($enFile, "<?php return " . var_export($en, true) . ";", LOCK_EX);
    }

    $value = $en[$key];

    if ($locale !== 'en') {
        $translated = googleTranslate($value, 'en', $locale);
        $translations[$key] = $translated;
        file_put_contents($file, "<?php return " . var_export($translations, true) . ";", LOCK_EX);
        return $translated;
    }

    return $value;
}

function googleTranslate($text, $source = 'auto', $target = 'en')
{
    $url = "https://translate.googleapis.com/translate_a/single?client=gtx"
        . "&sl=" . $source
        . "&tl=" . $target
        . "&dt=t&q=" . urlencode($text);

    $response = @file_get_contents($url);

    if (!$response) {
        return $text;
    }

    $result = json_decode($response, true);

    return $result[0][0][0] ?? $text;
}

function spanishDateToEnglish($text)
{
    $months = [
        'enero'=>'January',
        'febrero'=>'February',
        'marzo'=>'March',
        'abril'=>'April',
        'mayo'=>'May',
        'junio'=>'June',
        'julio'=>'July',
        'agosto'=>'August',
        'septiembre'=>'September',
        'octubre'=>'October',
        'noviembre'=>'November',
        'diciembre'=>'December'
    ];

    $days = [
        'lunes'=>'Monday',
        'martes'=>'Tuesday',
        'miércoles'=>'Wednesday',
        'jueves'=>'Thursday',
        'viernes'=>'Friday',
        'sábado'=>'Saturday',
        'domingo'=>'Sunday'
    ];

    $text = strtolower($text);

    $dayName = '';
    $monthName = '';
    $date = '';

    foreach ($days as $es=>$en) {
        if (strpos($text,$es) !== false) {
            $dayName = $en;
        }
    }

    foreach ($months as $es=>$en) {
        if (strpos($text,$es) !== false) {
            $monthName = $en;
        }
    }

    preg_match('/\d+/', $text, $match);
    $date = $match[0] ?? '';

    return trim($dayName . ' ' . $monthName . ' ' . $date);
}


function englishDateToSpanish($text , $lang)
{
    if($lang == 'en')
    {
        return $text;
    }

    $months = [
        'january'   => 'enero',
        'february'  => 'febrero',
        'march'     => 'marzo',
        'april'     => 'abril',
        'may'       => 'mayo',
        'june'      => 'junio',
        'july'      => 'julio',
        'august'    => 'agosto',
        'september' => 'septiembre',
        'october'   => 'octubre',
        'november'  => 'noviembre',
        'december'  => 'diciembre'
    ];

    $days = [
        'monday'    => 'lunes',
        'tuesday'   => 'martes',
        'wednesday' => 'miércoles',
        'thursday'  => 'jueves',
        'friday'    => 'viernes',
        'saturday'  => 'sábado',
        'sunday'    => 'domingo'
    ];

    $textLower = strtolower($text);
    $result = $text;

    foreach ($days as $en => $es) {
        if (strpos($textLower, $en) !== false) {
            $result = str_ireplace($en, $es, $result);
        }
    }

    foreach ($months as $en => $es) {
        if (strpos($textLower, $en) !== false) {
            $result = str_ireplace($en, $es, $result);
        }
    }

    return $result;
}

function translateTitleToSpanish(){

    $db = \Config\Database::connect();
    
    $session = \Config\Services::session();
    $sessionData = $session->get();
    
    $lang = isset($sessionData['site_lang']) && !empty($sessionData['site_lang']) 
        ? $sessionData['site_lang'] 
        : 'en';

    if($lang == 'en'){
        return;
    }

    $builder = $db->table('tbl_tags');

    $builder->select('id, title, title_'.$lang);
   // $builder->where('title = title_'.$lang); // only where both columns are same

    $builder->where("BINARY title = BINARY title_$lang", null, false);


    $query = $builder->get()->getResultObject();

    foreach ($query as $row) {

        $title = $row->title;

        $translatedTitle = googleTranslate($title, 'en', $lang);

       
        if($title == $translatedTitle){
            $translatedTitle = $translatedTitle.'1';
        }
        
        $updateBuilder = $db->table('tbl_tags');
        $updateBuilder->where('id', $row->id);
        $updateBuilder->update([
            'title_'.$lang => $translatedTitle
        ]);

        // echo $title;
        // echo $translatedTitle;
        // echo $row->id;
        // exit;
    }
}