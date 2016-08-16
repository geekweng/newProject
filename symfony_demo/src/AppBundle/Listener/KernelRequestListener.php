<?php
namespace AppBundle\Listener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelRequestListener
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) && ($request->getMethod() == 'POST')) {
            $whiteList = array('/editor/upload', '/cas/user_profile/push', '/callback/logout');

            if (in_array($request->getPathInfo(), $whiteList)) {
                return;
            }

            if ($request->isXmlHttpRequest()) {
                $token = $request->headers->get('X-CSRF-Token');
            } else {
                $token = $request->request->get('_csrf_token', '');
            }

            $this->deleteCsrfToken($request);
            $expectedToken = $this->container->get('security.csrf.token_manager')->getToken('site');

            if ($token != $expectedToken->getValue()) {
                $response = $this->container->get('templating')->renderResponse('TopxiaWebBundle:Default:message.html.twig', array(
                    'type'     => 'error',
                    'message'  => '页面已过期，请重新提交数据！',
                    'goto'     => '',
                    'duration' => 0
                ));

                $event->setResponse($response);
            }
        }
    }

    private function deleteCsrfToken($request)
    {
        if ($request->getPathInfo() != '/login_check') {
            $request->request->remove('_csrf_token');
        }
    }
}
