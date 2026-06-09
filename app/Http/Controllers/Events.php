<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Virtualevent;
use App\Models\Event;
use App\Models\Connection;
use Illuminate\Support\Facades\DB;

class Events extends Controller
{
    //
    function virtualevents(){
		$event = new Event();
    	$data['pagetitle'] = "Manage Virtual Events";
    	$data['pageId'] = 'managevirtualevents';
    	$data['pendingVirtualEvents'] = $event->virtualEventsByStatus('Pending');
    	return view("events.virtualevents",$data);
    }
    function virtualeventssave(Request $req){
    	$virtualEvent = new Virtualevent();
    	
		$event = new Event();

    	if($req->input('deleteevent'))
    	{
    		$response = $virtualEvent->deleteVirtualEvent($req->input('delete_event_id'));
			$event->deleteEvent($req->input('delete_event_id'));
			return $response;
    	}
    	if($req->input('newevent'))
    	{
	    	$response = $virtualEvent->saveNewEvent($req->input());

	    	if($response['validation']==true){
	    		$event = new Event();
	    		$event->saveEventFromVirtualEvent($req->input(),$response['saved_event_id']);
	    	}
	    	return $response;
    	}
    	if($req->input('editevent')){

	    	$response = $virtualEvent->editEvent($req->input());

	    	if($response['validation']==true){
	    		$event = new Event();
	    		$event->editFromVirtualEvent($req->input(),$response['saved_event_id']);
	    	}
	    	return $response;	
    	}
    }
    function editVirtualEvent($virtualeventid){

		$event = new Virtualevent();
    	$data['pagetitle'] = "Edit Virtual Event";
    	$data['pageId'] = 'managevirtualevents';
    	$data['eventInfo'] = $event->eventDetails($virtualeventid);
    		if(count($data['eventInfo'])==0) { return redirect("/virtual_events"); }
    	return view("events.editvirtualevent",$data);
    }
    function virutalEventParticipate($virtualeventid)
    {

        $event = new Virtualevent();
        $data['pagetitle'] = "Participate In Virtual Event";
        $data['pageId'] = 'eventdetails';
        $data['eventInfo'] = $event->eventDetails($virtualeventid);
            if(count($data['eventInfo'])==0) { return redirect("/dashboard"); }
        return view("events.virutaleventParticipate",$data);
    }
    function connectionRequestForVirtualMeeting(Request $req)
    {
         if($req->ajax())
        {
            if($req->input('connectToUserId')!=NULL)
            {
            $userId = loggedInUserId();
            $participant1UserId = $userId;
            $participant2UserId = $req->input('connectToUserId');
            $eventId = $req->input('virtualEventId');
                $errors = array();

$isUserParticipatingCHeck = DB::table('virtual_event_participants')->select('*')->where('userid',$userId)->get()->toArray();

            if(count($isUserParticipatingCHeck)==0){
                $errors[] = "Please click on participate event to join the event";
            }            

            $participantAlreadyAssignedCHeck = DB::table('virtual_event_requests')->select('*')->whereRaw('((participant1_userid =\''.$userId.'\' AND participant2_userid=\''.$participant2UserId.'\' ) OR (participant2_userid =\''.$userId.'\' AND participant1_userid=\''.$participant2UserId.'\' )) AND (event_id = \''.$eventId.'\')')->get()->toArray();
            
            
            if(count($participantAlreadyAssignedCHeck)>0){
                $errors[] = "You are already participating in this virtual event with this user";
            }

            $fromDateTime = $req->input('fromdatetime').":01";

            $toDateTime = $req->input('todatetime').":00";
            
            $eventDetails = DB::table('virtual_events')->where('event_id',$eventId)->get()->toArray();
            if(count($eventDetails)==0){
             $errors[] = "Invalid Event ID";   
            }
            else{
                $eventfromTime = $eventDetails[0]->from_date_time;
                $eventtoTime = $eventDetails[0]->to_date_time;
                if(strtotime($fromDateTime) < strtotime($eventfromTime)){
                    $errors[] = "From Time should be within the specified time limits of the event";   
                }
                if(strtotime($toDateTime) > strtotime($eventtoTime)){
                    $errors[] = "To Time should be within the specified time limits of the event";   
                }
            }

            $messageForRequest = $req->input('meetingIntro');
            $participant2Details = DB::table('users')->select('*')->where('userid', $participant2UserId)->get()->toArray();
            $participant1Details = DB::table('users')->select('*')->where('userid', $userId)->get()->toArray();

            if(count($participant2Details)==0){
                $errors[] = "Invalid User ID Passed";
            }

            if(count($participant1Details)==0){
                $errors[] = "Invalid User ID Passed";
            }
            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
                $randomRequestId = random_generator(10,'numeric').time();
            DB::table('virtual_event_requests')->insert(
                array('event_id' => $eventId,
                    'participant1_userid' => $participant1UserId,
                    'intro_message' => $messageForRequest,
                    'participant2_userid' => $participant2UserId,
                    'from_date_time' => $fromDateTime,
                    'to_date_time' => $toDateTime,
    'request_id' => ($randomRequestId), 'approval_status' => 'Pending', 'created_at' => (date("Y-m-d H:i:s")), 
    'updated_at' => (date("Y-m-d H:i:s"))

));


                $mailObj['subject'] = "Request For Virtual B2B Meeting from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name);
                $mailObj['to_address']= $participant2Details[0]->email;
                $mailObj['to_name'] = $participant2Details[0]->first_name ." ".$participant2Details[0]->last_name;
                $mailObj['htmlcontent'] = "<p>You have recieved a new B2B Virtual Meeting request from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name)."</p>";
                $mailObj['htmlcontent'] .= "<p>Scheduled for ".(date("M d,Y h:i A",strtotime($fromDateTime)))." to ".(date("M d,Y h:i A",strtotime($toDateTime)))."</p>";
                $clickHereLink = "<a href=\"".(url('/virtual_event_member_request'))."/".$randomRequestId."\">Click Here</a>";
                $mailObj['htmlcontent'] .= "<p>Please ".$clickHereLink." to respond to the request</p>If you are unable to open the link, copy the following link and paste it in the browser<br />".(url('/virtual_event_member_request'))."/".$randomRequestId;


                sendHtmlEmail($mailObj);
        $response['validation']  = true;
        $response['message'] = "Your request Has been sent";


            }
            return $response;
        }
            if($req->input('approvePendingRequest')!=NULL)
            {

$userId = loggedInUserId();
            $approvePendingRequestId = $req->input('approvePendingRequest');
            
            $errors = array();
$vmRequestInfo = DB::table('virtual_event_requests')->select('*')->where('request_id',$approvePendingRequestId)->get()->toArray();
            
            if(count($vmRequestInfo)==0)
            {
                $errors[] = "Invalid meeting request ID passed";
            }
            elseif($vmRequestInfo[0]->approval_status != 'Pending'){
                $errors[] = "The status of this meeting request is ".$vmRequestInfo[0]->approval_status;
            }

            if($userId!=$vmRequestInfo[0]->participant1_userid && $userId!=$vmRequestInfo[0]->participant2_userid )
            {
             $errors[] = "You are not the participant of this meeting request";   
            }


            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
                $randomMeetingId = random_generator(10,'numeric').time();
                
                DB::table('virtual_event_requests')->where('request_id',$approvePendingRequestId)
       ->update(
        array('approval_status' => 'Accepted', 'created_at' => (date("Y-m-d H:i:s")), 
    'updated_at' => (date("Y-m-d H:i:s"))
           
        )
   ); 
            DB::table('virtual_event_meetings')->insert(
                array('event_id' => $vmRequestInfo[0]->event_id,
                    'meeting_id' => $randomMeetingId,
                    'participant1_userid' => $vmRequestInfo[0]->participant1_userid,
                    'notes' => '',
                    'meeting_link' => '',
                    'participant2_userid' => $vmRequestInfo[0]->participant2_userid,
                    'from_date_time' => $vmRequestInfo[0]->from_date_time,
                    'to_date_time' => $vmRequestInfo[0]->to_date_time,
                    'status' => 'Pending',
                    'created_at' => (date("Y-m-d H:i:s")), 
    'updated_at' => (date("Y-m-d H:i:s"))

));


            $secondPerson = $vmRequestInfo[0]->participant1_userid;
            if($userId == $vmRequestInfo[0]->participant1_userid){ $secondPerson = $vmRequestInfo[0]->participant2_userid;}
            $participant2UserId = $secondPerson;

            $participant2Details = DB::table('users')->select('*')->where('userid', $participant2UserId)->get()->toArray();
            $participant1Details = DB::table('users')->select('*')->where('userid', $userId)->get()->toArray();


                $mailObj['subject'] = "Request Approved For Virtual B2B Meeting from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name);
                $mailObj['to_address']= $participant2Details[0]->email;
                $mailObj['to_name'] = $participant2Details[0]->first_name ." ".$participant2Details[0]->last_name;
                $mailObj['htmlcontent'] = "<p>Your B2B Virtual Meeting request has been approved by ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name)."</p>";
                $mailObj['htmlcontent'] .= "<p>Scheduled for ".(date("M d,Y h:i A",strtotime($vmRequestInfo[0]->from_date_time)))." to ".(date("M d,Y h:i A",strtotime($vmRequestInfo[0]->to_date_time)))."</p>";
                

            $firstUser = $userId;
            $secondUser = $participant2UserId;
            $connection = new Connection();
            $connection->saveNewConnection($firstUser,$secondUser);

                sendHtmlEmail($mailObj);
        $response['validation']  = true;
        $response['message'] = "Your meeting has been successfully Scheduled";

                
            }
            return $response;
        }
        if($req->input('reschedulePendingRequest')){


            if($req->input('fromdatetime')==''){
                $errors[] = "From date and time is mandatory";
            }else{
            $fromDateTime = $req->input('fromdatetime').":01";    
            }


            if($req->input('todatetime')==''){
                $errors[] = "To date and time is mandatory";
            }else{
            $toDateTime = $req->input('todatetime').":00";
            }
            

            

            $userId = loggedInUserId();
            $approvePendingRequestId = $req->input('reschedulePendingRequest');
            
            $errors = array();
$vmRequestInfo = DB::table('virtual_event_requests')->select('*')->where('request_id',$approvePendingRequestId)->get()->toArray();
            
            if(count($vmRequestInfo)==0)
            {
                $errors[] = "Invalid meeting request ID passed";
            }
            elseif($vmRequestInfo[0]->approval_status != 'Pending'){
                $errors[] = "The status of this meeting request is ".$vmRequestInfo[0]->approval_status;
            }

            if($userId!=$vmRequestInfo[0]->participant1_userid && $userId!=$vmRequestInfo[0]->participant2_userid )
            {
             $errors[] = "You are not the participant of this meeting request";   
            }


            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
                
                 DB::table('virtual_event_requests')->where('request_id',$approvePendingRequestId)
                       ->update(
                        array('approval_status' => 'Rescheduled',
                    'updated_at' => (date("Y-m-d H:i:s"))
                           
                        )
                   );
                

            $secondPerson = $vmRequestInfo[0]->participant1_userid;
            if($userId == $vmRequestInfo[0]->participant1_userid){ $secondPerson = $vmRequestInfo[0]->participant2_userid;}
            $participant2UserId = $secondPerson;

            $participant2Details = DB::table('users')->select('*')->where('userid', $participant2UserId)->get()->toArray();
            $participant1Details = DB::table('users')->select('*')->where('userid', $userId)->get()->toArray();

                   $randomRequestId = random_generator(10,'numeric') .time();
            DB::table('virtual_event_requests')->insert(
                array('event_id' => $vmRequestInfo[0]->event_id,
                    'participant1_userid' => $userId,
                    'intro_message' => $req->input('meetingIntro'),
                    'participant2_userid' => $participant2UserId,
                    'from_date_time' => $fromDateTime,
                    'to_date_time' => $toDateTime,
                    'parent_request_id' => $vmRequestInfo[0]->request_id,
    'request_id' => ($randomRequestId), 'approval_status' => 'Pending', 'created_at' => (date("Y-m-d H:i:s")), 
    'updated_at' => (date("Y-m-d H:i:s"))

));


                $mailObj['subject'] = "Rescheduled Request For Virtual B2B Meeting from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name);
                $mailObj['to_address']= $participant2Details[0]->email;
                $mailObj['to_name'] = $participant2Details[0]->first_name ." ".$participant2Details[0]->last_name;
                $mailObj['htmlcontent'] = "<p>You have recieved a rescheduled B2B Virtual Meeting request from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name)."</p>";
                $mailObj['htmlcontent'] .= "<p>Scheduled for ".(date("M d,Y h:i A",strtotime($fromDateTime)))." to ".(date("M d,Y h:i A",strtotime($toDateTime)))."</p>";
                $clickHereLink = "<a href=\"".(url('/virtual_event_member_request'))."/".$randomRequestId."\">Click Here</a>";
                $mailObj['htmlcontent'] .= "<p>Please ".$clickHereLink." to respond to the request</p>If you are unable to open the link, copy the following link and paste it in the browser<br />".(url('/virtual_event_member_request'))."/".$randomRequestId;


                sendHtmlEmail($mailObj);
        $response['validation']  = true;
        $response['message'] = "Your request Has been sent";


            }
            return $response;
        }
        if($req->ajax()){

        if($req->input('deletePendingRequest'))
        {

                $userId = loggedInUserId();
            $approvePendingRequestId = $req->input('deletePendingRequest');
            
            $errors = array();
            $vmRequestInfo = DB::table('virtual_event_requests')->select('*')->where('request_id',$approvePendingRequestId)->get()->toArray();


            if(count($vmRequestInfo)==0)
            {
                $errors[] = "Invalid meeting request ID passed";
            }
            elseif($vmRequestInfo[0]->approval_status != 'Pending'){
                $errors[] = "The status of this meeting request is ".$vmRequestInfo[0]->approval_status;
            }

            if($userId!=$vmRequestInfo[0]->participant1_userid && $userId!=$vmRequestInfo[0]->participant2_userid )
            {
             $errors[] = "You are not the participant of this meeting request";   
            }


            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
                
                DB::table('virtual_event_requests')->where('request_id',$approvePendingRequestId)->delete(); 
        $response['validation']  = true;
        $response['message'] = "Meeting request has been rejected";

                
            }
            return $response;
        }
        if($req->input('rejectPendingRequest')!=NULL){
                
$userId = loggedInUserId();
            $approvePendingRequestId = $req->input('rejectPendingRequest');
            
            $errors = array();
$vmRequestInfo = DB::table('virtual_event_requests')->select('*')->where('request_id',$approvePendingRequestId)->get()->toArray();
            
            if(count($vmRequestInfo)==0)
            {
                $errors[] = "Invalid meeting request ID passed";
            }
            elseif($vmRequestInfo[0]->approval_status != 'Pending'){
                $errors[] = "The status of this meeting request is ".$vmRequestInfo[0]->approval_status;
            }

            if($userId!=$vmRequestInfo[0]->participant1_userid && $userId!=$vmRequestInfo[0]->participant2_userid )
            {
             $errors[] = "You are not the participant of this meeting request";   
            }


            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
                
                DB::table('virtual_event_requests')->where('request_id',$approvePendingRequestId)
       ->update(
        array('approval_status' => 'Rejected', 'created_at' => (date("Y-m-d H:i:s")), 
    'updated_at' => (date("Y-m-d H:i:s"))
           
        )
   ); 

            $secondPerson = $vmRequestInfo[0]->participant2_userid;
            if($userId == $vmRequestInfo[0]->participant1_userid){ $secondPerson = $vmRequestInfo[0]->participant2_userid;}
            $participant2UserId = $secondPerson;

            $participant2Details = DB::table('users')->select('*')->where('userid', $participant2UserId)->get()->toArray();
            $participant1Details = DB::table('users')->select('*')->where('userid', $userId)->get()->toArray();


                $mailObj['subject'] = "Request Rejected For Virtual B2B Meeting from ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name);
                $mailObj['to_address']= $participant2Details[0]->email;
                $mailObj['to_name'] = $participant2Details[0]->first_name ." ".$participant2Details[0]->last_name;
                $mailObj['htmlcontent'] = "<p>Your B2B Virtual Meeting request has been Rejected by ".($participant1Details[0]->first_name ." ".$participant1Details[0]->last_name)."</p>";
                $mailObj['htmlcontent'] .= "<p>Scheduled for ".(date("M d,Y h:i A",strtotime($vmRequestInfo[0]->from_date_time)))." to ".(date("M d,Y h:i A",strtotime($vmRequestInfo[0]->to_date_time)))."</p>";
                


                sendHtmlEmail($mailObj);
        $response['validation']  = true;
        $response['message'] = "Meeting request has been rejected";

                
            }
            return $response;

        }
        }
    }
   
    }
    function virutalEventRequestRespond($virtualeventrequestid){
        
        if($virtualeventrequestid!=NULL){

        $data['pagetitle'] = "Respond To Virtual Event Request";
        $data['pageId'] = 'eventdetails';
        $data['requestInfo'] = DB::table('virtual_event_requests')->select('*')->where('request_id',$virtualeventrequestid)->get()->toArray();

        if(count($data['requestInfo'])!=0)
        {
            $eventId = $data['requestInfo'][0]->event_id;
            $participant1 = $data['requestInfo'][0]->participant1_userid;
            $participant2 = $data['requestInfo'][0]->participant2_userid;
            if(loggedInUserId() != $participant2){
          return redirect("/dashboard");       
            }
            $data['eventInfo'] = DB::table('virtual_events')->select('*')->where('event_id',$eventId)->get()->toArray();
        }
            if(count($data['requestInfo'])==0) { return redirect("/dashboard"); }
        
        return view("events.respondVirtualEventRequest",$data);
        
        }
    }

     function virtualEventConfirmParticipation(Request $req){
        if($req->ajax())
        {
            $userId = loggedInUserId();
            $participantAlreadyAssignedCHeck = DB::table('virtual_event_participants')->select('*')->whereRaw('userid =\''.$userId.'\' AND event_id=\''.$req->input('virtualeventid').'\'')->get()->toArray();
            
            $errors = array();

            if(count($participantAlreadyAssignedCHeck)>0){
                $errors[] = "You are already participating in this virtual event";
            }

            if(count($errors)>0)
            {

                $response['validation']  = false;
                $response['message'] = "We Found Some Errors";
                $response['errors'] = $errors;

            }else{
            DB::table('virtual_event_participants')->insert(
                array('userid' => $userId,
    'event_id' => $req->input('virtualeventid'), 'approval_status' => 'Accepted', 'created_at' => (date("Y-m-d H:i:s")), 'updated_at' => (date("Y-m-d H:i:s"))

));

        $response['validation']  = true;
        $response['message'] = "Your participation is registered with us";


            }
            return $response;
        }
        }
        function upcomingEvent()
        {
            $upcomingVirtualEvent = DB::table('virtual_events')->select('id')->orderBy('from_date_time')->limit(1)->get()->toArray();
            $virtualEvent = new Virtualevent();
            if(count($upcomingVirtualEvent)!=0){
            $virutalEventInfo = $virtualEvent->find($upcomingVirtualEvent[0]->id);
                $data['virutalEventInfo'] = $virutalEventInfo;
                return view("events.eventdisplay",$data);

                }
        }
        
        function eventPage($eventid){

            $upcomingVirtualEvent = DB::table('virtual_events')->select('id')->where('event_id',$eventid)->orderBy('from_date_time')->limit(1)->get()->toArray();
            $virtualEvent = new Virtualevent();
            if(count($upcomingVirtualEvent)!=0){
            $virutalEventInfo = $virtualEvent->find($upcomingVirtualEvent[0]->id);
                $data['virutalEventInfo'] = $virutalEventInfo;
                return view("events.eventdisplay",$data);

                }
        }
}
