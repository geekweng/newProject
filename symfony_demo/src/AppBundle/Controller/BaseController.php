<?php

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
  /**
     * 创建消息提示响应.
     *
     * @param  string     $type     消息类型：info, warning, error
     * @param  string     $message  消息内容
     * @param  string     $title    消息抬头
     * @param  int        $duration 消息显示持续的时间
     * @param  string     $goto     消息跳转的页面
     * @return Response
     */

    protected function createMessageResponse($type, $message, $title = '', $duration = 0, $goto = null)
    {
        if (!in_array($type, array('info', 'warning', 'error'))) {
            throw new \RuntimeException('type不正确');
        }

        return $this->render('TopxiaWebBundle:Default:message.html.twig', array(
            'type'     => $type,
            'message'  => $message,
            'title'    => $title,
            'duration' => $duration,
            'goto'     => $goto
        ));
    }

    protected function getTargetPath($request)
    {
        if ($request->query->get('goto')) {
            $targetPath = $request->query->get('goto');
        } elseif ($request->getSession()->has('_target_path')) {
            $targetPath = $request->getSession()->get('_target_path');
        } else {
            $targetPath = $request->headers->get('Referer');
        }

        if ($targetPath == $this->generateUrl('login', array(), true)) {
            return $this->generateUrl('homepage');
        }

        if (empty($targetPath)) {
            $targetPath = $this->generateUrl('homepage', array(), true);
        }

        return $targetPath;
    }

    protected function getCurrentUser()
    {
        return $this->getServiceKernel()->getCurrentUser();
    }

    protected function createMessageModalResponse($type, $message, $title = '', $duration = 0, $goto = null)
    {
        return $this->render('TopxiaWebBundle:Default:message-modal.html.twig', array(
            'type'     => $type,
            'message'  => $message,
            'title'    => $title,
            'duration' => $duration,
            'goto'     => $goto
        ));
    }

    protected function checkLogin()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw $this->createAccessDeniedException();
        }
    }

    protected function isLogin()
    {
        return $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    protected function setFlashMessage($level, $message)
    {
        $this->get('session')->getFlashBag()->add($level, $message);
    }

    protected function setting($name, $default = null)
    {
        return $this->get('e3c.twig.web_extension')->getSetting($name, $default);
    }

    protected function isPluginInstalled($name)
    {
        return $this->get('e3c.twig.web_extension')->isPluginInstaled($name);
    }

    protected function createNamedFormBuilder($name, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, 'form', $data, $options);
    }

    protected function sendEmail($to, $title, $body, $format = 'text')
    {
        $format = $format == 'html' ? 'text/html' : 'text/plain';

        $config = $this->setting('mailer', array());

        if (empty($config['enabled'])) {
            return false;
        }

        $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
            ->setUsername($config['username'])
            ->setPassword($config['password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $email = \Swift_Message::newInstance();
        $email->setSubject($title);
        $email->setFrom(array($config['from'] => $config['name']));
        $email->setTo($to);

        if ($format == 'text/html') {
            $email->setBody($body, 'text/html');
        } else {
            $email->setBody($body);
        }

        $mailer->send($email);

        return true;
    }

    protected function createJsonResponse($data)
    {
        return new JsonResponse($data);
    }

    /**
     * JSONM
     * https://github.com/lifesinger/lifesinger.github.com/issues/118.
     */
    protected function createJsonmResponse($data)
    {
        $response = new JsonResponse($data);
        $response->setCallback('define');

        return $response;
    }

    protected function getServiceKernel()
    {
        return ServiceKernel::instance();
    }

    protected function getUserService()
    {
        return $this->getServiceKernel()->createService('User.UserService');
    }

    protected function getLogService()
    {
        return $this->getServiceKernel()->createService('System.LogService');
    }
}
