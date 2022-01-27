<?php

namespace App\Controller\Admin;

use App\Entity\Origin;
use App\Form\OriginDeleteType;
use App\Form\OriginType;
use App\Manager\OriginManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OriginController extends AbstractController
{
    protected OriginManager $manager;

    public function __construct(OriginManager $originManager)
    {
        $this->manager = $originManager;
    }

    final public function list(): Response
    {
        $origins = $this->manager->loadMultiple();

        return $this->render('admin/origin/list.html.twig', ['title' => 'Origins', 'origins' => $origins]);
    }

    final public function add(Request $request): Response
    {
        $origin = new Origin();
        $form = $this->createForm(OriginType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();
            $this->mapFromFormData($origin);
            if (!$origin->getName()) {
                $this->manager->save($origin);
            }

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/add.html.twig', ['title' => 'Add an origin', 'form' => $form->createView()]);
    }

    final public function edit(string $origin_id, Request $request): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(OriginType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();
            $this->mapFromFormData($origin);
            if ($this->manager->exists($origin->getName())) {
                $this->manager->save($origin);
            }

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/edit.html.twig', ['title' => 'Edit ' . $origin->getLabel() . ' origin', 'form' => $form->createView()]);
    }

    final public function delete(string $origin_id, Request $request): Response
    {
        if (!$origin = $this->manager->load($origin_id)) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(OriginDeleteType::class, $origin);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $origin = $form->getData();
            $this->manager->delete($origin);

            return $this->redirectToRoute('admin.origin.list');
        }

        return $this->render('admin/origin/delete.html.twig', ['title' => 'Delete ' . $origin->getLabel() . ' origin', 'form' => $form->createView(), 'origin' => $origin->getHost()]);
    }

    final private function mapFromFormData(Origin $origin): void
    {
        $ignoreHeaders = $origin->getIgnoreHeaders();
        foreach ($ignoreHeaders as $entry => $ignoreHeader) {
            if (is_array($ignoreHeader) && isset($ignoreHeader['value'])) {
                $ignoreHeaders[$entry] = $ignoreHeader['value'];
            }
        }
        $origin->setIgnoreHeaders($ignoreHeaders);

        $ignoreContent = $origin->getIgnoreContent();
        foreach ($ignoreContent as $entry => $ignoreContentEntry) {
            if (is_array($ignoreContentEntry) && isset($ignoreContentEntry['value'])) {
                $ignoreContent[$entry] = $ignoreContentEntry['value'];
            }
        }
        $origin->setIgnoreContent($ignoreContent);
    }
}
