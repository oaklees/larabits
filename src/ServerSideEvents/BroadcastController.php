<?php

namespace Larabits\ServerSideEvents;

use Illuminate\Routing\Controller;

class BroadcastController extends Controller
{
    /**
     * @var ServerSideEventBroadcaster $broadcaster
     */
    protected $broadcaster;

    /**
     * Inject broadcaster on creation.
     *
     * @param ServerSideEventBroadcaster $broadcaster
     */
    public function __construct(ServerSideEventBroadcaster $broadcaster)
	{
		$this->broadcaster = $broadcaster;
	}

	/**
	 * Begin listening on the channel defined in the route URI
     *
	 * @param string $channel
	 * @internal param \Illuminate\Http\Request $request
	 */
    public function listen($channel = 'default')
    {
		$this->broadcaster->listen($channel);
    }
}
