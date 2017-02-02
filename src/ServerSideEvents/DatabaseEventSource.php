<?php

namespace Larabits\ServerSideEvents;

use Illuminate\Database\Connection;
use Carbon\Carbon;

class DatabaseEventSource implements EventSource
{
    /**
     * Our default database connection.
     *
     * @var \Illuminate\Database\Connection $db
     */
    protected $db;

    /**
     * Channel to listen to.
     *
     * @var string $channel
     */
    protected $channel;

    /**
     * Database table for event storage.
     *
     * @var string $table
     */
    protected $table = 'broadcasts';

    /**
     * Set the database connection on creation.
     *
     * @param \Illuminate\Database\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Define which channel to source events from.
     *
     * @param string $channel
     * @return void
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * Collect events for broadcast.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect()
    {
        return collect(
            $this->db->table($this->table)
                ->where('channel',$this->channel)
                ->where('created_at','>',Carbon::now()->addSecond(-10))
                ->get([
                    'id',
                    'event',
                    'payload',
                ])
        )->sortBy('id');
    }

    /**
     * Store a pushed event to the database.
     *
     * @param $event
     */
    public function push($event)
    {
        $this->db->table($this->table)->insert($event);
    }

    /**
     * Clear the source of expired events.
     *
     * @return void
     */
    public function purge()
    {
        $this->db->table($this->table)
            ->where('created_at', '<', Carbon::now()->addSecond(-10))
            ->delete();
    }

}