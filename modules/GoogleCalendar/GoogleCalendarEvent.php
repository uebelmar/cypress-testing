<?php
namespace SpiceCRM\modules\GoogleCalendar;

class GoogleCalendarEvent
{
    public $kind = 'calendar#event';
    public $etag;
    public $id;
    public $status;
    public $htmlLink;
    public $created;
    public $updated;
    public $summary;
    public $creator = [];
    public $organizer = [];
    public $start = [];
    public $end = [];
    public $iCalUID;
    public $sequence;
    public $reminders = [];

    protected $fillable = [
        'kind', 'etag', 'id', 'status', 'htmlLink', 'created', 'updated', 'summary', 'creator', 'organizer', 'start',
        'end', 'iCalUID', 'sequence', 'reminders', 'description', 'location', 'attendees',
    ];

    /**
     * GoogleCalendarEvent constructor.
     * @param $params
     */
    public function __construct($params) {
        foreach ($params as $attribute => $value) {
            $this->fillParams($attribute, $value);
        }
    }

    /**
     * fillParams
     *
     * Fills in object parameters
     *
     * TODO needs some way to fill nested/more complex params, coz now it just assigns everything
     *
     * @param $attribute
     * @param $value
     */
    private function fillParams($attribute, $value) {
        if (in_array($attribute, $this->fillable)) {
            $this->$attribute = $value;
        }
    }

    /**
     * getParams
     *
     * Returns an array of all attributes that are not empty
     *
     * @return array
     */
    private function getParams() {
        $params = [];

        foreach ($this->fillable as $attribute) {
            if (!empty($this->$attribute)) {
                $params[$attribute] = $this->$attribute;
            }
        }

        return $params;
    }

    /**
     * serialize
     *
     * Serializes the object for the REST Request
     *
     * @return false|string
     */
    public function serialize() {
        return json_encode($this->getParams());
    }



}
