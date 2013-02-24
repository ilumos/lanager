<?php
namespace LANager;
use Aware;

class Event extends Aware {

     public function type()
     {
          return $this->has_one('LANager\Event_type');
     }

     public function manager()
     {
          return $this->belongs_to('LANager\User');
     }


}