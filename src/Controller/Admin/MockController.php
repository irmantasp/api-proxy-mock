<?php

namespace App\Controller\Admin;

use App\Manager\MockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MockController extends AbstractController
{
    protected MockManager $manager;

    public function __construct(MockManager $mockManager)
    {
        $this->manager = $mockManager;
    }

    final public function all(Request $request): Response
    {
        $mocks = $this->manager->loadMultiple();

        return $this->render('admin/mock/list.html.twig', ['title' => 'Records', 'mocks' => $mocks]);
    }

    final public function list(string $origin_id, Request $request): Response
    {
        $mocks = $this->manager->loadMultiple([], $origin_id);

        return $this->render('admin/mock/list.html.twig', ['title' => 'Records', 'mocks' => $mocks]);
    }

    final public function add(Request $request): Response
    {

    }

    final public function addWithoutOrigin(Request $request): Response
    {

    }

    final public function edit(string $origin_id, string $mock_id, Request $request)
    {

    }

    final public function delete(string $origin_id, string $mock_id, Request $request)
    {

    }

}