<?php

namespace Project\Infrastructure\Laravel\API\Controllers\Administrators;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Project\Modules\Administrators\Commands\LogoutCommand;
use Project\Modules\Administrators\Queries\AuthorizedAdminQuery;
use Project\Infrastructure\Laravel\API\Controllers\BaseApiController;

class AdminsController extends BaseApiController
{
    private Guard $auth;

    public function __construct()
    {
        $this->auth = Auth::guard('admin');
    }

    public function create(Requests\CreateAdmin $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $id], 'Admin created');
    }

    public function update(Requests\UpdateAdmin $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $request->get('id')], 'Admin updated');
    }

    public function delete(Requests\DeleteAdmin $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $request->get('id')], 'Admin deleted');
    }

    public function get(Requests\GetAdmin $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }

    public function list(Requests\AdminsList $request)
    {
        $data = $this->dispatchQuery($request->getQuery());
        return $this->success($data);
    }

    public function authorized()
    {
        if (!$this->auth->check()) {
            return $this->error(401, 'You does not authorized');
        }

        $data = $this->dispatchQuery(new AuthorizedAdminQuery);
        return $this->success($data);
    }

    public function login(Requests\AuthorizeAdmin $request)
    {
        if ($this->auth->check()) {
            return $this->error(401, 'You already authorized');
        }

        $this->dispatchCommand($request->getCommand());
        return $this->success([], 'You authorized successfully');
    }

    public function logout()
    {
        if (!$this->auth->check()) {
            return $this->error(401, 'You does not authorized');
        }

        $this->dispatchCommand(new LogoutCommand);
        return $this->success([], 'Bye');
    }
}