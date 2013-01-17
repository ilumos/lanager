<?php
namespace LANager;
use Aware;

class Event_type extends Aware {

     public function event()
     {
          return $this->belongs_to('LANager\Event');
     }

}