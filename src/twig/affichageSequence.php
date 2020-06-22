<?php

namespace App\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class affichageSequence extends AbstractExtension
{
    public function getFilters()
    {
        return [new TwigFilter('affichageSequence', [$this, 'affichageSequenceFilter'])];
    }
    
    public function affichageSequenceFilter($content)
    {
        $tableau=preg_split("/[\/\\\]/",$content); //https://www.php.net/manual/fr/function.preg-split.php : permet
        return $tableau;
    }
    
}
