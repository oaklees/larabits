<?php

namespace Larabits\ServerSideEvents;

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class SseBroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Broadcasting\BroadcastManager $broadcastManager
     * @return void
     */
	
    public function boot(BroadcastManager $broadcastManager)
    {
        // Bind the database implementation of the EventSource contract.
        $this->app->bind(
            EventSource::class,
            DatabaseEventSource::class
        );

        // Register the event broadcaster prior to extending the Broadcast Manager.
    	$this->app->singleton(ServerSideEventBroadcaster::class, function($app){
	        $config = $app["config"]["broadcasting.connections.database"];
            return new ServerSideEventBroadcaster($config,$app[EventStore::class]);
	    });

    	// Extend the Broadcast Manager with a 'database' implementation. Return
        // previously registered singleton.
        $broadcastManager->extend('database',function($app){
            return $app[ServerSideEventBroadcaster::class];
        });

        // Define the routes in the application.
	    Broadcast::connection()->routes();
    }
}
