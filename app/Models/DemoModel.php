<?php

namespace Models;

class DemoModel extends BaseModel{
    public function getdate(){
        
        // $this->db->query("..."); - DBAL
        
        return date('Y-m-d H:i:s');
    }
}