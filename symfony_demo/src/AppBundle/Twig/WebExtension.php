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

    public function getFunctions()
    {
      return array(
        new \Twig_SimpleFunction('setting', array($this, 'getSetting')),
        new \Twig_SimpleFunction('filepath', array($this, 'getFilePath'))
      );
    }

    public function getSetting($name, $default = null)
    {
        $names = explode('.', $name);

        $name = array_shift($names);

        if (empty($name)) {
            return $default;
        }

        $value = ServiceKernel::instance()->createService('System.SettingService')->get($name);

        if (!isset($value)) {
            return $default;
        }

        if (empty($names)) {
            return $value;
        }

        $result = $value;

        foreach ($names as $name) {
            if (!isset($result[$name])) {
                return $default;
            }

            $result = $result[$name];
        }

        return $result;
    }

    private function getPublicFilePath($path, $defaultKey = false, $absolute = false)
    {
        $assets = $this->container->get('templating.helper.assets');

        if (empty($path)) {
            $defaultSetting = $this->getSetting("default", array());

            if ((($defaultKey == 'course.png' && array_key_exists('defaultCoursePicture', $defaultSetting) && $defaultSetting['defaultCoursePicture'] == 1)
                || ($defaultKey == 'avatar.png' && array_key_exists('defaultAvatar', $defaultSetting) && $defaultSetting['defaultAvatar'] == 1))
                && (array_key_exists($defaultKey, $defaultSetting)
                    && $defaultSetting[$defaultKey])
            ) {
                $path = $defaultSetting[$defaultKey];
                return $this->parseUri($path, $absolute);
            } else {
                $path = $assets->getUrl('assets/img/default/'.$defaultKey);
                return $this->addHost($path, $absolute);
            }
        }

        return $this->parseUri($path, $absolute);
    }

    public function getName()
    {
        return 'app_web_twig';
    }

}
