<?php

/**
* Model dealing with business logics
*/
class Model_Todo_Logic
{
    static $status_cache;
    static $status_map;
    static $status_bimap;

    private function __construct() {
        // static member only
    }

    public static function initialize()
    {
        self::$status_cache = array_map(
            function ($row) {
                return $row->name;
            }, Model_Todo_Status::query()->select('name')->get()
        );
        self::$status_map   = Util_Array::to_map('ucwords', self::$status_cache);
        self::$status_bimap = Util_Array::bimap(self::$status_cache);
    }

    /**
     * Fetch all alive ToDos from DB
     * @return ORM object
     */
    private static function fetch_alive()
    {
        return Model_Todo::query()->where('deleted', '=', false);
    }

    /**
     * Fetch TODOs from DB
     * @return iterator of TODOs
     */
    static function fetch_todo()
    {
        return self::fetch_alive()->get();
    }

    static function fetch_filtered_by($status_id)
    {
        return self::fetch_alive()->where('status_id', '=', $status_id)->get();
    }

    static function fetch_ordered_by($attr, $dir)
    {
        return self::fetch_alive()->order_by($attr, $dir)->get();
    }

    /**
     * update todo by id
     * @param  int $id      of Todo
     * @param  [attrivute => value, ...] $updates attributes to be updated
     */
    static function alter($id, $updates)
    {
        // suppose no missing id
        $todo = Model_Todo::find($id);
        foreach ($updates as $attr => $value) {
            $todo->$attr = $value;
        }
        $todo->save();
    }

    public static function chop_datetime($datetime)
    {
        if (is_null($datetime)) {
            return [null, null];
        }
        $date = new DateTime($datetime);
        return [$date->format('Y-m-d'), $date->format('H:i')];
    }
}

// initiaize static member
Model_Todo_Logic::initialize();