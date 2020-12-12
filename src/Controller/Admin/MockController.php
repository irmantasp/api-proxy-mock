<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MockController extends AbstractController
{

    final public function all(Request $request): Response
    {

    }

    final public function list(string $origin_id, Request $request): Response
    {

    }

}