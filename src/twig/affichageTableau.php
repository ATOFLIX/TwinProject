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
        $tableau=array_chunk($content, 6);  //https://www.php.net/manual/fr/function.array-chunk.php : sépare le tableau $content en plusieurs tableaux ayant chacun 6 valeurs
        return $tableau;
    }
    
}
