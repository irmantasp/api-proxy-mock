<?php

namespace App\Controller\Admin;

use App\Entity\Origin;
use App\Form\OriginDeleteType;
use App\Form\OriginType;
use App\Service\HostConfigServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OriginController extends AbstractController
{
    protected HostConfigServiceInterface $hostConfig;

    public function __construct(HostConfigServiceInterface $hostConfigService)
    {
        $this->hostConfig = $hostConfigService;
    }

    final public function list(): Response
    {
        $origins = $this->hostConfig->getOrigins();

        return $this->render('admin/origin/list.html.twig', ['title'=> 'Origins', 'secondary_title', 'origins' => $origins]);
    }

    final public function add(Request $request): Response
    {
        $origin = new Origin();
        $form = $this->createForm(OriginType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/add.html.twig', ['title' => 'Add an origin', 'form' => $form->createView()]);
    }

    final public function edit(string $origin, Request $request): Response
    {
        if (!$origin = $this->hostConfig->getOrigin($origin)) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(OriginType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/edit.html.twig', ['title' => 'Edit ' . $origin->getHost() . ' origin', 'form' => $form->createView()]);
    }

    final public function delete(string $origin, Request $request): Response
    {
        if (!$origin = $this->hostConfig->getOrigin($origin)) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(OriginDeleteType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/delete.html.twig', ['title' => 'Delete ' . $origin->getHost() . ' origin', 'form' => $form->createView(), 'origin' => $origin->getHost()]);
    }
}
