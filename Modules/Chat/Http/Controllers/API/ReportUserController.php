<?php

namespace Modules\Chat\Http\Controllers\API;

use Modules\Chat\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Modules\Chat\Http\Requests\CreateReportUserRequest;
use Modules\Chat\Repositories\ReportedUserRepository;

/**
 * Class ReportUserController
 */
class ReportUserController extends AppBaseController
{
    /**
     * @var ReportedUserRepository
     */
    private $reportedUserRepo;

    /**
     * ReportUserController constructor.
     *
     * @param  ReportedUserRepository  $reportedUserRepository
     */
    public function __construct(ReportedUserRepository $reportedUserRepository)
    {
        $this->reportedUserRepo = $reportedUserRepository;
    }

    /**
     * @param  CreateReportUserRequest  $request
     * @return JsonResponse
     */
    public function store(CreateReportUserRequest $request)
    {
        $this->reportedUserRepo->createReportedUser($request->all());

        return $this->sendSuccess('User reported successfully.');
    }
}
