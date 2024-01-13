<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Console;

use Illuminate\Console\Command;
use Project\Common\Administrators\Role;
use Project\Common\Events\DispatchEventsTrait;
use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Common\Events\DispatchEventsInterface;
use Project\Modules\Administrators\Repository\AdminsRepositoryInterface;

class MakeAdminCommand extends Command implements DispatchEventsInterface
{
    use DispatchEventsTrait;

    protected $description = 'Create new admin';
    protected $signature = 'app-admin:create {login?} {name?}';

    public function __construct(
        private AdminsRepositoryInterface $admins,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name') ?: $this->ask('Enter name');
        $login = $this->argument('login') ?: $this->ask('Enter login');
        $password = $this->secret('Enter password');
        $passwordConfirmation = $this->secret('Confirm password');

        if ($password !== $passwordConfirmation) {
            $this->error('Wrong password confirmation');
            return;
        }

        $admin = $this->createAdmin($name, $login, $password);
        $this->dispatchEvents($admin->flushEvents());
        $this->info('Admin created');
    }

    private function createAdmin(
        string $name,
        string $login,
        string $password
    ): Admin {
        $admin = new Admin(
            AdminId::next(),
            $name,
            $login,
            $password,
            [Role::ADMIN]
        );

        $this->admins->add($admin);
        return $admin;
    }
}