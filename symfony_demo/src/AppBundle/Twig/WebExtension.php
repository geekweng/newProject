<?php
namespace AppBundle\Twig;

class WebExtension extends \Twig_Extension
{
    protected $container;

    protected $pageScripts;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'app_web_twig';
    }

}
