<?php



namespace App\twig;



use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class affichageSequence extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('affichage', [$this, 'affichageFilter'])];
    }
    
    public function affichageFilter($content)
    {
        $tableau=preg_split("/[\/\\\]/",$content);
        return $tableau;
    }
    
}
