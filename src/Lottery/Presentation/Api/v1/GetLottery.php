<?php

declare(strict_types=1);

namespace App\Lottery\Presentation\Api\v1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lottery', methods: ['GET'])]
final class GetLottery extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['eee' => 'eee']);
    }
}
