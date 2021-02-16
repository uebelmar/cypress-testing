<?php
namespace SpiceCRM\modules\GoogleTasks;

class GoogleTask
{
    protected $fillable = ['id', 'dueDate', 'title', 'notes', 'status'];

    /**
     * GoogleTask constructor.
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

    private function getParams() {
        $params = [];

        foreach ($this->fillable as $attribute) {
            if (!empty($this->$attribute)) {
                $params[$attribute] = $this->$attribute;
            }
        }

        return $params;
    }

    public function serialize() {
        return json_encode($this->getParams());
    }
}
