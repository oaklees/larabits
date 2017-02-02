<?php

namespace Larabits\ServerSideEvents;

use Illuminate\Routing\Controller;

class BroadcastController extends Controller
{
    /**
     * Our event broadcaster instance for this request.
     *
     * @var \App\ServerSideEvents\ServerSideEventBroadcaster $broadcaster
     */
    protected $broadcaster;

    /**
     * Inject broadcaster on creation.
     *
     * @param \App\ServerSideEvents\ServerSideEventBroadcaster $broadcaster
     */
    public function __construct(ServerSideEventBroadcaster $broadcaster)
	{
		$this->broadcaster = $broadcaster;
	}

	/**
	 * Begin listening on the channel defined in the route URI
     *
	 * @param string $channel
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * @internal param \Illuminate\Http\Request $request
	 */
    public function listen($channel = 'default')
    {
		$this->broadcaster->listen($channel);
    }
}
