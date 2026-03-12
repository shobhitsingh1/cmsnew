<?php

namespace App\Controllers;

class Language extends BaseController
{


    public function switch($lang)
    {
        session()->set('site_lang',$lang);
        return redirect()->back();
    }
}

?>