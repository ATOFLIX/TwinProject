<?php


namespace App\twig;



use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class triTableau extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('tri', [$this, 'triFilter'])];
    }
    
    public function triFilter($content)
    {
        natsort($content);
        return $content;
    }

}