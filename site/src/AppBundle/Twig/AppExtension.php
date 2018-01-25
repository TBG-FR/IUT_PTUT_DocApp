<?php

namespace AppBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('strpad', [$this, 'strpad']),
            new TwigFilter('urlparams', [$this, 'urlparams'])
        ];
    }

    public function strpad($number, $pad_length, $pad_string)
    {
        return str_pad($number, $pad_length, $pad_string, STR_PAD_LEFT);
    }

    public function urlparams($url_string)
    {
        $exp = explode('?', $url_string);
        if(count($exp) >= 2) {
            $params = [];
            $exp = explode('&', $exp[1]);
            foreach($exp as $value) {
                $exp2 = explode('=', $value);
                $params[$exp2[0]] = $exp2[1];
            }

            return $params;
        }

        return [];
    }
}