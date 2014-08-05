<?php

/**
 * This is the default API CRUD class to allow developers to write CRUD controllers without
 * the mess of actually creating CRUD controllers. All that the developer needs to do
 * is to is extend the Controller_Crud and initialize the variables $_orm_model, $_name, and $_plural_name
 * This class is abstract only to stop it from being used by a without the proper data.
 *
 * @author Ryan Dawkins
 */
abstract class Controller_Crud extends \Fuel\Core\Controller_Rest
{

    /**
     * The model class for the orm
     * @var string
     */
    protected static $_orm_model;

    /**
     * @var string The name we return the objects as
     */
    protected static $_name;

    /**
     * @var string Plural name the object mapped to
     */
    protected static $_plural_name;

    public function before()
    {
        parent::before();

        // Defaults to json if no extension given
        if(empty(Input::extension())) {
            $this->format = "json";
        }
    }

    /**
     * This method allows creation by using the following URL:
     * HTTP POST: /api/{object}/create
     *
     * Which will return some JSON serialized data
     */
    public function post_create()
    {
        // Grabbing the JSON data
        $json_body = Input::json();

        // Orm creation based off of the Controller created
        $orm = new $this::$_orm_model();
        $orm->from_array($json_body);
        $orm->save();

        return $this->response($this->singular_array($orm));
    }

    /**
     * Receives a primary key to read/list all orm objects
     *
     * Example 1: (read)
     * HTTP GET: /api/{object}/1
     *
     * Example 2: (list)
     * HTTP GET: /api/{object}
     *
     * @param int|null $pk Allows the user to list or get a specific ORM object
     */
    public function get_read($pk=null)
    {
        $data = null;
        if($pk)
        {
            // Create singular ORM object
            $model = new $this::$_orm_model();
            $orm = $model::find($pk);

            // Checking to see if ORM is good
            if(!$orm) {
                $data = $this->not_found();
            } else {
                $data = $this->singular_array($orm);
            }
        }
        else
        {
            // Grabbing all ORMs
            $model = new $this->orm_model();
            $orms = $model::find("all");
            $data = $this->plural_array($orms);
        }

        return $this->response($data);
    }

    /**
     * Allows the updating of orm objects by a given primary key
     *
     * Example:
     * HTTP POST: /api/{object}/1/update
     *
     * @param int $pk The primary key of the object
     */
    public function post_update($pk)
    {
        // Getting json body
        $json_body = Input::json();

        // Creating ORM from primary key
        $model = new $this::$_orm_model();
        $orm = $model::find($pk);

        // Creating output
        $data = null;
        if($orm) {
            $orm->from_array($json_body);
            $orm->save();

            $data = $this->singular_array($orm);
        } else {
            $data = $this->not_found();
        }

        return $this->response($data);
    }

    /**
     * Allows the deletion of an object by a given primary key and also returns the old data before deletion
     *
     * Example:
     * HTTP GET: /api/{object}/1/delete
     *
     * @param int $pk The primary key of the orm
     */
    public function get_delete($pk)
    {
        $model = new $this::$_orm_model();
        $orm = $model::find($pk);

        $data = null;
        if($orm) {
            $data = $this->singular_array($orm);
            $orm->delete();
        } else {
            $data = $this->not_found();
        }

        return $this->response($data);
    }

    /**
     * Returns an array serialized with the name of the object pointing to an array of the data
     *
     *
     * @param Orm\Model $orm The orm to be serialized
     * @return array An array with the name pointing to the orm in array form
     */
    protected function singular_array(Orm\Model $orm)
    {
        return array(
            "message" => "Sucess",
            "code" => "200",
            $this::$_name => $orm->to_array(),
        );
    }

    /**
     * Creates a plural array of the orms given
     *
     * @param array $orms An array of ORMs
     * @return array An array serialized version of the ORMs given
     */
    protected function plural_array($orms)
    {
        $data = array();
        foreach($orms as $orm) {
            $data[] = $this->singular_array($orm);
        }
        return array(
            "message" => "Sucess",
            "code" => "200",
            $this::$_plural_name => $data,
        );
    }

    /**
     * Lets the API client know that the object was not found
     *
     * @return array Static array that the object was not found
     */
    protected function not_found()
    {
        return array(
            "message" => "Sorry not found",
            "code" => "201",
        );
    }
}
