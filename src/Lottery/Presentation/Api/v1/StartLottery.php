<?php

declare(strict_types=1);

namespace App\Lottery\Presentation\Api\v1;

use App\Lottery\Application\Dto\Request\StartLotteryRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/start', methods: ['POST'])]
final class StartLottery extends AbstractController
{
    public function __invoke(
        #[MapRequestPayload] StartLotteryRequest $lottery
    ): JsonResponse {
        dd($lottery);
        return new JsonResponse();
    }
}
