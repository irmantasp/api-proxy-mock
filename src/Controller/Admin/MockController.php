<?php

namespace App\Controller\Admin;

use App\Entity\Mock;
use App\Form\MockType;
use App\Form\OriginMockType;
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

    final public function add(string $origin_id, Request $request): Response
    {
        $mock = new Mock();
        $mock->setOriginId($origin_id);

        $form = $this->createForm(OriginMockType::class, $mock);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mock = $form->getData();
            $this->manager->save($mock);

            return $this->redirectToRoute('admin.mock.list', ['origin_id' => $origin_id]);
        }

        return $this->render('admin/mock/add.html.twig', ['title' => 'Add mock record', 'form' => $form->createView()]);
    }

    final public function addWithoutOrigin(Request $request): Response
    {
        $mock = new Mock();
        $form = $this->createForm(MockType::class, $mock);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mock = $form->getData();
            $this->manager->save($mock);

            return $this->redirectToRoute('admin.mock.list.complete');
        }

        return $this->render('admin/mock/add.html.twig', ['title' => 'Add mock record', 'form' => $form->createView()]);
    }

    final public function edit(string $origin_id, string $mock_id, Request $request)
    {

    }

    final public function delete(string $origin_id, string $mock_id, Request $request)
    {

    }

}