<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Controllers\User;

use Spatie\ViewModels\ViewModel;
use Webloyer\App\Service\User\GetUsersService;
use Webloyer\Infra\App\DataTransformer\User\UsersLaravelLengthAwarePaginatorDataTransformer;
use Webloyer\Infra\Framework\Laravel\App\Http\Requests\User\IndexRequest;
use Webloyer\Infra\Framework\Laravel\Resources\ViewModels\User\IndexViewModel;

class IndexController extends BaseController
{
    /**
     * Handle the incoming request.
     *
     * @param IndexRequest $request
     * @return ViewModel
     */
    public function __invoke(IndexRequest $request): ViewModel
    {
        assert($this->service instanceof GetUsersService);
        assert($this->service->usersDataTransformer() instanceof UsersLaravelLengthAwarePaginatorDataTransformer);
        $this->service->usersDataTransformer()->setPerPage(10);
        $users = $this->service->execute();

        return (new IndexViewModel($users))->view('webloyer::user.index');
    }
}
