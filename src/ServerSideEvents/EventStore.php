<?php

namespace Larabits\ServerSideEvents;

use Illuminate\Support\Collection;
use Carbon\Carbon;

class EventStore
{
    /**
     * Events that are currently available for broadcasting.
     *
     * @var Collection $availableEvents
     */
    protected $availableEvents;

    /**
     * Events that have already been broadcast
     *
     * @var Collection $sentEvents
     */
    protected $sentEvents;

    /**
     * Source of our broadcast events.
     *
     * @var \App\ServerSideEvents\EventSource $source
     */
    protected $source;

    /**
     * Initialise sentEvents collection and the source of our events.
     *
     * @param \App\ServerSideEvents\EventSource $source
     */
    public function __construct(EventSource $source)
    {
        $this->sentEvents = collect([]);
        $this->source = $source;
    }

    /**
     * Determine which channel to store events from.
     *
     * @param $channel
     */
    public function listenFor($channel)
    {
        $this->source->setChannel($channel);
    }

    /**
     * Collect the currently available events to broadcast.
     *
     */
    public function fetch()
    {
        $this->availableEvents = $this->source->collect();
    }

    /**
     * Determine whether we have any events that we can broadcast.
     *
     * @return bool
     */
    public function hasEvents()
    {
        return (bool) $this->eventsAwaitingBroadcast()->count();
    }

    /**
     * Mark as event as sent for this session.
     *
     * @param $event
     */
    public function markAsSent($event)
    {
        $this->sentEvents->push($event->id);

        if ($this->sourceEventsPurgeDue())
        {
            $this->source->purge();
        }
    }

    /**
     * Determine whether a purge of expired events should be done.
     *
     * @return bool
     */
    public function sourceEventsPurgeDue()
    {
        return mt_rand(0,10) < 5;
    }

    /**
     * Get the next event that is ready to be broadcast
     *
     */
    public function next()
    {
        return $this->eventsAwaitingBroadcast()->first();
    }

    /**
     * Determine events that are valid to be broadcast.
     *
     * @return Collection
     */
    protected function eventsAwaitingBroadcast()
    {
        // If we have already broadcast an event, remove it from the available events
        // and return the total count. Greater than 0 asserts we have events
        // to broadcast.
        return $this->availableEvents->reject(function ($event) {
            return $this->sentEvents->contains($event->id);
        });
    }

    /**
     * Take a Laravel broadcast and push it on to the source storage mechanism.
     *
     * @param $channels
     * @param $event
     * @param $payload
     */
    public function pushToSource($channels, $event, $payload)
    {
        collect($channels)->each(function($channel) use ($event, $payload){
            $this->source->push([
                'channel'   => $channel->name,
                'event'     => $event,
                'payload'   => json_encode($payload),
                'created_at'=> Carbon::now(),
            ]);
        });
    }
}