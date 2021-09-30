<?php

namespace App\Services;

use Mobile_Detect;
use Symfony\Component\HttpFoundation\Request;


class CheckDeviceFromUser
{
    public function checkDeviceFromUser():string {


        if (!class_exists('Mobile_Detect')) { return 'isClassic'; }

        $detect = new Mobile_Detect;

        if ($detect->isTablet()){
            return 'isClassic';
        }
        elseif ($detect->isMobile()){
            return 'isMobile';
        }
        else {
            return 'isClassic';
        }

    }


}