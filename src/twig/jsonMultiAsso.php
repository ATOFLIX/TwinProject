<?php



namespace App\twig;



use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class jsonMultiAsso extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('jsonMultiAsso', [$this, 'jsonMultiAssoFilter'])];
    }
    
    public function jsonMultiAssoFilter($content)
    {
        $tableau=json_decode($content, TRUE);  //https://www.php.net/manual/fr/function.json-decode.php : Décode la chaine $content
        $retour=[];
        foreach($tableau as $un)
        {
            foreach($un as $deux)
            {
                array_push($retour,$deux);  //https://www.php.net/manual/fr/function.array-push.php : emplie les valeurs de la varialobe $ deux a la fin du tableau $retour
            }
        }
        return array_chunk($retour, 6); //https://www.php.net/manual/fr/function.array-chunk.php : retourne des tableaux associatifs ayant chacun 6 valeurs
    }
    
}
