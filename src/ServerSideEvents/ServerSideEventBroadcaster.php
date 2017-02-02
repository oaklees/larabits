<?php

namespace Larabits\ServerSideEvents;

use Carbon\Carbon;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Contracts\Broadcasting\Broadcaster as BroadcastContract;

class ServerSideEventBroadcaster extends Broadcaster implements BroadcastContract
{
	/**
	 * Poll frequency (microseconds) at which to query event existence.
     *
     * @var integer $poll
	 */
	protected $poll;

    /**
     * Local event store.
     *
     * @var EventStore $events
     */
    protected $events;

    /**
     * The last event transmission time.
     *
     * @var \Carbon\Carbon $lastTransmission
     */
    protected $lastTransmission = null;

    /**
     * Interval (seconds) at which to send 'keep alive' events.
     *
     * @var integer $keepAliveInterval
     */
    protected $keepAliveInterval = 25;

    /**
     * Set configuration and initialise the event store.
     *
     * @param $config
     * @param \App\ServerSideEvents\EventStore $store
     */
	public function __construct($config, EventStore $store)
    {
        $this->poll   = $config['poll'] * 1000000;
        $this->events = $store;
    }

	/**
	 * Define which channel to listen to.
	 *
	 * @param $channel
	 */
	public function listen($channel)
	{
		$this->events->listenFor($channel);
		$this->initialise();
	}

	/**
	 * Define endpoint for broadcasting events.
	 *
	 * @param array | null $attributes
	 */
	public function routes($attributes = null)
	{
		$attributes = $attributes ?: ['middleware' => ['web']];

		app('router')->group($attributes, function ($router) {
			$router->get('/broadcasts/{channel}', 'App\ServerSideEvents\BroadcastController@listen');
		});
	}

	/**
	 * Broadcast loop.
     *
	 */
	public function initialise()
	{
		$this->setHeaders();

		while(1) {

            $this->events->fetch();

			if ($this->events->hasEvents())
			{
				$this->send($this->events->next());
				continue;
			}

			$this->keepConnectionAlive();

            usleep($this->poll);
		}
	}

	/**
	 * Set headers for the event stream.
	 *
	 */
	private function setHeaders()
	{
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');

        ob_flush();
		flush();
	}

	/**
	 * Send an event over the open stream.
	 *
	 * @param $event
	 */
	private function send($event)
	{
		$this->lastTransmission = Carbon::now();

        $id      = $event->id;
		$name    = $event->event;
        $message = $event->payload;

		echo "id: $id\n";
		echo "event: $name\n";
		echo "data: $message\n\n";

        ob_flush();
		flush();

        $this->events->markAsSent($event);
	}

	/**
	 * Maintain the event stream connection.
	 *
	 */
	private function keepConnectionAlive()
	{
        // Property lastTransmission won't be set if a connection to the stream
        // has been made whilst there are no events to be broadcast. To keep
        // an eye on when this was, set this to the current time.
		$this->lastTransmission = $this->lastTransmission ?: Carbon::now();

		if ($this->keepAliveRequired())
		{
			$this->send((object) [
				'id' => 0,
				'event' => 'keepAlive',
				'payload' => "You're still listening, right? Just checking..",
			]);

			$this->lastTransmission = Carbon::now();
		}
	}

    /**
     * Determine whether a 'keep alive' transmission is required.
     *
     * @return bool
     */
    private function keepAliveRequired()
    {
        return Carbon::now()->diffInSeconds($this->lastTransmission) > $this->keepAliveInterval;
	}

	/**
	 * Authenticate the incoming request for a given channel.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return void
	 */
	public function auth($request)
	{
		//
	}

	/**
	 * Return the valid authentication response.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  mixed $result
	 * @return void
	 */
	public function validAuthenticationResponse($request, $result)
	{
		//
	}

	/**
	 * Broadcast the given event.
	 *
	 * @param  array $channels
	 * @param  string $event
	 * @param  array $payload
	 * @return void
	 */
	public function broadcast(array $channels, $event, array $payload = [])
	{
        $this->events->pushToSource($channels, $event, $payload);
	}
}