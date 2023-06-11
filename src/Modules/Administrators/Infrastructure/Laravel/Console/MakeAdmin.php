<?php

namespace Project\Modules\Administrators\Infrastructure\Laravel\Console;

use Illuminate\Console\Command;
use Project\Common\Administrators\Role;
use Project\Modules\Administrators\Entity\Admin;
use Project\Modules\Administrators\Entity\AdminId;
use Project\Modules\Administrators\Repository\AdminRepositoryInterface;

class MakeAdmin extends Command
{
    public function __construct(
        private AdminRepositoryInterface $admins
    ) {
        parent::__construct();
    }

    protected $description = 'Create new admin';
    protected $signature = 'app-admin:create {login?} {name?}';

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

    private function dispatchEvents(array $events): void
    {
        foreach ($events as $event) {
            app()->make('EventBus')->dispatch($event);
        }
    }
}