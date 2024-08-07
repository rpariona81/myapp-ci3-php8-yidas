<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class My_model extends yidas\Model {

	protected $primaryKey = 'id';
    const CREATED_AT = 'created_time';
    const UPDATED_AT = 'updated_time';
}
