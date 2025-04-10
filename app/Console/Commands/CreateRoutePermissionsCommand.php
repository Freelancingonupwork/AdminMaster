<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class CreateRoutePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create-permission-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a permission routes.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();
        foreach ($routes as $route) {
            $action = $route->getAction();
            $routeName = $route->getName();

            if (!empty($routeName) && isset($action['middleware']) && is_array($action['middleware']) && in_array('web', $action['middleware'])) {
                $permission = Permission::where('name', $routeName)->first();

                if (is_null($permission)) {
                    Permission::create(['name' => $routeName]);
                }
            }
        }

        $this->info('Permission routes added successfully.');
    }
}
