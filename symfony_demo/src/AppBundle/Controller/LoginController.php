<?php

namespace AppBundle\Controller;

use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

Class LoginController extends BaseController
{
  public function indexAction(Request $request)
  {
    return $this->render('AppBundle:Login:index.html.twig',array());
  }
}
