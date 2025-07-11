<?php
if(!defined('ABSPATH'))
    die('You are not allowed to call this page directly.');

require_once SUPER_LINKS_HELPERS_PATH . '/vendor/autoload.php';
use wp_activerecord\ActiveRecord;

class SuperLinksPosts extends ActiveRecord
{

    protected static $table_name = 'posts';

}