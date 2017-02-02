<?php

namespace Larabits\ServerSideEvents;

interface EventSource
{
    /**
     * Define which channel to source events from.
     *
     * @param string $channel
     * @return void
     */
    public function setChannel($channel);

    /**
     * Collect events for broadcast.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect();

    /**
     * Clear the source of expired events.
     *
     * @return void
     */
    public function purge();

    /**
     * Push a broadcast event to the source data store.
     *
     * @param $event
     * @return void
     */
    public function push($event);

}