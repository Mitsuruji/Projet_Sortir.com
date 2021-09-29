<?php

namespace App\Services;

use Mobile_Detect;
use Symfony\Component\HttpFoundation\Request;


class CheckDeviceFromUser
{
    public function checkDeviceFromUser(Request $request):string {


        if (!class_exists('Mobile_Detect')) { return 'isClassic'; }

        $userAgent = $request->headers->get('User-Agent');
        $detect = new Mobile_Detect;

        if ($detect->isMobile($userAgent)){
            return 'isMobile';
        }
        elseif ($detect->isTablet($userAgent)){
            return 'isTablet';
        }
        else {
            return 'isClassic';
        }

    }


}