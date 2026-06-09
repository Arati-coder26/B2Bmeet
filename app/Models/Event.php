<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Event extends Model
{
    //
    public function saveEventFromVirtualEvent($input,$givenEventId){

			$countryCheck = $this->where('event_id', '=', $givenEventId)->get()->toArray();
		
			if(count($countryCheck)==0){

				$this->event_id = $givenEventId;
				$this->type = 'Virtual';
			$this->from_date_time = $input['fromdatetime'].":00";
			$this->to_date_time = $input['todatetime'].":00";
			
		    // $this->created_by = $loggedInUser;
		    $this->save();	
			}

			
    }
    public function virtualEventsByStatus($status){

    	$virtualEventsByStatus =	DB::table('virtual_events')
            ->leftJoin('events', 'events.event_id', '=', 'virtual_events.event_id')
            ->where('virtual_events.status','Pending')
            ->get()->toArray();
        return ($virtualEventsByStatus);
    }
    public function editFromVirtualEvent($input,$givenEventId){
				
			$countryCheck = $this->where('event_id', '=', $givenEventId)->get()->toArray();
		if(count($countryCheck)!=0){
			$eventObj = $this->find($countryCheck[0]['id']);
				$eventObj->event_id = $givenEventId;
				$eventObj->type = 'Virtual';
			$eventObj->from_date_time = $input['fromdatetime'].":00";
			$eventObj->to_date_time = $input['todatetime'].":00";
			
		    // $this->created_by = $loggedInUser;
		    $eventObj->save();	
			}

    }
    public function deleteEvent($deleteEventId){

    	$errors = array();
    	
    	$country = $this->where('event_id','=',$deleteEventId)->get()->toArray();

    	if($country == false || ($country==null) || (is_array($country) && count($country)==0)){
    		$errors[] = "Invalid Event ID Passed";
    	}


    	
        if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{

        	$countryId = $country[0]['id'];
        	$country = $this->find($countryId);
		    $country->delete();
		    $response['validation'] = true;
		    $response['message'] = "Event has been deleted successfully.";
	    }

    }
}
