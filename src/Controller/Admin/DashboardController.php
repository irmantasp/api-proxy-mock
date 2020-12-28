<?php

namespace App\Controller\Admin;

use App\Manager\MockManager;
use App\Manager\OriginManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    private OriginManagerInterface $originManager;
    private MockManager $mockManager;

    public function __construct(OriginManagerInterface $originManager, MockManager $mockManager)
    {
        $this->originManager = $originManager;
        $this->mockManager = $mockManager;
    }

    final public function display(): Response
    {
        return $this->render(
            'admin/dashboard/item.html.twig',
            [
                'title' => 'Dashboard',
                'origins' => $this->getOrigins(),
                'mocks' => $this->getMocks(),
            ]
        );
    }

    private function getOrigins(): array
    {
        $origins = $this->originManager->loadMultiple();
        asort($origins);
        return array_slice($origins, 0, 5, true);
    }

    private function getMocks(): array
    {
        $mocks = $this->mockManager->loadMultiple();
        asort($mocks);
        return array_slice($mocks, 0, 10, true);
    }

}