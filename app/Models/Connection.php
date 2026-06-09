<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    //
    function saveNewConnection($firstUser,$secondUser)
    {
    	$existingConnectionCheck = $this->whereRaw("(userid1='".$firstUser."' AND userid2='".$secondUser."') OR (userid2='".$firstUser."' AND userid1='".$secondUser."')")->get()->toArray();
    	if(count($existingConnectionCheck)==0){

    		$this->userid1 = $firstUser;
    		$this->userid2 = $secondUser;
    		$this->status = "Accepted";
    		$this->request_sent_by = $firstUser;
    		$this->save();
    	}

    }
    function hasConnectionOfUser($forUser,$withUser){
                $existingConnectionCheck = $this->whereRaw("(userid1='".$forUser."' AND userid2='".$withUser."') OR (userid2='".$withUser."' AND userid1='".$forUser."')")->get()->toArray();
                if(count($existingConnectionCheck)!=0){
                        return true;
                }
                return false;

    }
}
