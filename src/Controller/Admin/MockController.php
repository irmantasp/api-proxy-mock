<?php

namespace App\Controller\Admin;

use App\Entity\Mock;
use App\Form\MockDeleteType;
use App\Form\MockType;
use App\Form\OriginMockType;
use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MockController extends AbstractController
{
    protected RequestMockManager $manager;
    protected OriginManagerInterface $originManager;

    public function __construct(RequestMockManager $mockManager, OriginManagerInterface $originManager)
    {
        $this->manager = $mockManager;
        $this->originManager = $originManager;
    }

    final public function all(Request $request): Response
    {
        $mocks = $this->manager->loadAll();

        return $this->render('admin/mock/list.html.twig', ['title' => 'Records', 'mocks' => $mocks, 'origin' => null]);
    }

    final public function list(string $origin_id, Request $request): Response
    {
        if (!$origin = $this->originManager->load($origin_id)) {
            throw new NotFoundHttpException();
        }

        $mocks = $this->manager->loadByOrigin($origin);

        return $this->render('admin/mock/list.html.twig', ['title' => 'Records for ' . $origin->getLabel() . ' origin', 'mocks' => $mocks, 'origin' => $origin]);
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
            $this->mapFromFormData($mock);

            $this->manager->save($mock);

            return $this->redirectToRoute('admin.mock.list.complete');
        }

        return $this->render('admin/mock/add.html.twig', ['title' => 'Add mock record', 'form' => $form->createView()]);
    }

    final public function edit(string $origin_id, string $mock_id, Request $request)
    {
        if (!$mock = $this->manager->load($mock_id, $origin_id)) {
            throw new NotFoundHttpException();
        }

        $this->mapToFormData($mock);
        $form = $this->createForm(OriginMockType::class, $mock);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mock = $form->getData();
            $this->mapFromFormData($mock);
            $this->manager->save($mock);

            return $this->redirectToRoute('admin.mock.list', ['origin_id' => $origin_id]);
        }

        return $this->render('admin/mock/edit.html.twig', ['title' => 'Edit mock for ' . $mock->getOrigin()->getHost() . $mock->getUri(), 'form' => $form->createView(), 'mock' => $mock]);
    }

    final public function delete(string $origin_id, string $mock_id, Request $request)
    {
        if (!$mock = $this->manager->load($mock_id, $origin_id)) {
            throw new NotFoundHttpException();
        }

        $this->mapToFormData($mock);
        $form = $this->createForm(MockDeleteType::class, $mock);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $mock = $form->getData();
            $this->manager->delete($mock);

            return $this->redirectToRoute('admin.mock.list', ['origin_id' => $origin_id]);
        }

        return $this->render('admin/mock/delete.html.twig', ['title' => 'Delete mock for ' . $mock->getOrigin()->getHost() . $mock->getUri(), 'form' => $form->createView(), 'mock' => $mock->getOrigin()->getHost() . $mock->getUri(), 'origin' => $origin_id]);
    }

    final private function mapFromFormData(Mock $mock): void {
        $headers = $mock->getHeaders();
        $header = reset($headers);
        if (isset($header['header'], $header['value'])) {
            $cleanHeaders = [];
            foreach ($headers as $entry) {
                $cleanHeaders[$entry['header']][] = $entry['value'];
            }
            $mock->setHeaders($cleanHeaders);
        }
    }

    final private function mapToFormData(Mock $mock): void {
        $headers = $mock->getHeaders();
        $header = reset($headers);
        if (!isset($header['header'], $header['value'])) {
            $cleanHeaders = [];
            foreach ($headers as $header => $values) {
                $cleanHeaders[] = [
                    'header' => $header,
                    'value' => reset($values),
                ];
            }
            $mock->setHeaders($cleanHeaders);
        }
    }

}