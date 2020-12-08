<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\AppBundle\AdminMethods;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('findOneBy', [$this, 'findOneBy']),
            new TwigFunction('getPowerAdminSection', [$this, 'getPowerAdminSection']),
        ];
    }

    public function findOneBy($repo, $options)
    {
        return $repo->findOneBy($options);
    }

    public function getPowerAdminSection($current_page, $value){

        $adminMethods = new AdminMethods();
        if(isset($adminMethods->getAdminPages()[$current_page][$value])) return $adminMethods->getAdminPages()[$current_page][$value];
        return 0;
    }
}