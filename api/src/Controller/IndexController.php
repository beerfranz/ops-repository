<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IndexController extends AbstractController
{

  /**
   * @Route("/", name="getIndex", methods={"GET"})
   */
  public function getIndex(Request $request): Response
  {
    return $this->render('index.html.twig');
  }

  /**
   * @Route("/whoami", name="getWhoami", methods={"GET"})
   */
  public function getWhoami(Request $request): JsonResponse
  {
    var_dump($this->getUser()); exit;
  }
}