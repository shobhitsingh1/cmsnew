<?php

namespace App\Controllers;

class Language extends BaseController
{


    public function switch($lang)
    {
        session()->set('site_lang', $lang);

        $previousUrl = previous_url();

        if (strpos($previousUrl, 'library/librarysingle.php') !== false || strpos($previousUrl, 'library/libraryseries.php') !== false) {
            return redirect()->to(base_url('/add_devotional.php'));
        }

        return redirect()->to($previousUrl);
    }
}

?>