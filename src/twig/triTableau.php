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
        natsort($content); //https://www.php.net/manual/fr/function.natsort.php : trie
        return $content;
    }

}
