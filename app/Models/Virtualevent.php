<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Virtualevent extends Model
{
    //
    protected $table = 'virtual_events';
    function saveNewEvent($input){
    	$errors = array();
    	$givenEventId = $input['eventid'];
	
		$loggedInUser  = loggedInUserId();
    	
    	if($givenEventId!=NULL)
		{
			$countryCheck = $this->where('event_id', '=', $givenEventId)->get()->toArray();
		

			if(count($countryCheck)!=0){
		    	$errors[] = "Event ID Already Exists";
		    }
		}
		else{
			do{
				$givenEventId = random_generator(8,'numeric');
			$countryCheck = $this->where('event_id', '=', $givenEventId)->get()->toArray();

			}while(count($countryCheck)!=0);
		}

		if(checkAuthForPage('createevents','createevents') == false){
			$errors[] = "You are not authorized to create events";
		}


            if(preg_match("/[^a-z_.\-0-9]/i", strtolower($givenEventId)))
            {
                    $errors[] = "Only small case alphabets,numbers are allowed in the Event id";
                    
            }

		if($input['fromdatetime']==''){
			$errors[] = "Please mention the from time for the event";	
		}
		if($input['todatetime']==''){
			$errors[] = "Please mention the from time for the event";	
		}


		if($input['eventtitle']==''){
			$errors[] = "Event Title is mandatory";	
		}
		
		if($input['eventdescription']==''){
			$errors[] = "Event Description is mandatory";	
		}
		if($input['slotDuration']==''){
			$errors[] = "Slot Duration is mandatory";	
		}


		    $response['saved_event_id'] = $givenEventId;
    	if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
			$this->event_id = $givenEventId;
			$this->event_title = $input['eventtitle'];
			$this->from_date_time = $input['fromdatetime'].":00";
			$this->to_date_time = $input['todatetime'].":00";
			$this->event_description = $input['eventdescription'];
			$this->slot_duration = $input['slotDuration'];
			$this->created_by = $loggedInUser;
			$this->updated_by = $loggedInUser;
			
		    // $this->created_by = $loggedInUser;
		    $this->save();
		    $response['validation'] = true;
		    $response['message'] = "Event Saved.";
		    }

    	

		    return $response;

    }
    function editEvent($input){
    	$errors = array();
    	$givenEventId = $input['edit_virtual_event_id'];
	
		$loggedInUser  = loggedInUserId();

		$registeredEventId = "";    	
    	if($givenEventId!=NULL)
		{
			$countryCheck = $this->where('id', '=', $givenEventId)->get()->toArray();
		

			if(count($countryCheck)==0){
		    	$errors[] = "Invalid Event ID Passed";
		    }else{
		    	$registeredEventId = $countryCheck[0]['event_id'];
		    }
		}

		if(checkAuthForPage('createevents','createevents') == false){
			$errors[] = "You are not authorized to create events";
		}

            if(preg_match("/[^a-z_.\-0-9]/i", strtolower($registeredEventId)))
            {
                    $errors[] = "Only small case alphabets,numbers are allowed in the Event id";
                    
            }

		if($input['fromdatetime']==''){
			$errors[] = "Please mention the from time for the event";	
		}
		if($input['todatetime']==''){
			$errors[] = "Please mention the from time for the event";	
		}


		if($input['eventtitle']==''){
			$errors[] = "Event Title is mandatory";	
		}
		
		if($input['eventdescription']==''){
			$errors[] = "Event Description is mandatory";	
		}

		if($input['slotDuration']==''){
			$errors[] = "slot duration is mandatory";	
		}


		    $response['saved_event_id'] = $registeredEventId;
    	if(count($errors)!=0)
        {

        $response['validation']  = false;
        $response['message'] = "We Found Some Errors";
        $response['errors'] = $errors;

        }else{
        	$virtualEvent = $this->find($givenEventId);
        	
        	$virtualEvent->event_title = $input['eventtitle'];
			$virtualEvent->from_date_time = $input['fromdatetime'].":00";
			$virtualEvent->to_date_time = $input['todatetime'].":00";
			$virtualEvent->event_description = $input['eventdescription'];
			$virtualEvent->slot_duration = $input['slotDuration'];
			$virtualEvent->updated_by = $loggedInUser;
			
		    // $this->created_by = $loggedInUser;
		    $virtualEvent->save();
		    $response['validation'] = true;
		    $response['message'] = "Event Saved.";
		    }

    	

		    return $response;

    }
    function eventDetails($eventId)
    {
    	$country = $this->where('event_id','=',$eventId)->get()->toArray();
    	return $country;

    }
    function deleteVirtualEvent($eventId){

    	$errors = array();
    	$country = $this->where('event_id','=',$eventId)->get()->toArray();
    	

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
		    return $response;

    }
}
