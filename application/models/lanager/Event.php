<?php
namespace lanager;
use Aware;

class Event extends Aware {

     public function type()
     {
          return $this->has_one('lanager\Event_type');
     }

     public function manager()
     {
          return $this->belongs_to('lanager\User');
     }


}