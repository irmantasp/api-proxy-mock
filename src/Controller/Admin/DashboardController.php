<?php

namespace App\Controller\Admin;

use App\Manager\OriginManagerInterface;
use App\Manager\RequestMockManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    private OriginManagerInterface $originManager;
    private RequestMockManager $mockManager;

    public function __construct(OriginManagerInterface $originManager, RequestMockManager $mockManager)
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
        $mocks = $this->mockManager->loadAll();
        $this->sortByDate($mocks);
        return array_slice($mocks, 0, 10, true);
    }

    private function sortByDate(array &$mocks): void
    {
        uasort($mocks, static function ($first, $second) {
            return $second->getDate() <=> $first->getDate();
        });
    }

}