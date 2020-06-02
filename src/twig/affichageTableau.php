<?php



namespace App\twig;



use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class affichageTableau extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('chunkTableau', [$this, 'chunkTableauFilter'])];
    }
    
    public function chunkTableauFilter($content)
    {
        //$nom1=explode(",", $content);
        $tableau=array_chunk($content, 6);
        return $tableau;
    }
    
}
