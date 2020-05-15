<?php

namespace App\twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class jsonDecode extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('json_decode', [$this, 'json_decodeFilter'])];
    }
    
    public function json_decodeFilter($content)
    {
        
        return json_decode($content);
    }
    
}
