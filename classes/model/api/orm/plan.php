<?php

class Model_Api_Orm_Plan extends Orm\Model
{
    protected static $_properties = array("plan_id", "name");
    protected static $_table_name = "plan";
    protected static $_primary_key = array("plan_id");
}