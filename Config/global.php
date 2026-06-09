<?php
   
return [
  
    'pagination_records' => 10,
  
    'user_type' => ['Participant', 'Admin'],
    'default_username' => 'admin',
    'default_password'	=> 'password',
    'session_logout_time'	=> (60*60),
    'site_name' => 'B2B Match',
    'permissionsVal' =>array(
		    'createevents' => "Events - Create Events",
            'deleteevent' => "Events - Create Events",
            'masterdatamanagement' => "Master Data Management - Countries, Cities"
		    
		),
    'permissions' => array(
			'createevents' => false,
            'deleteevent' => false,
            'masterdatamanagement' => false,
			
		),
    'user_profile_pic_folder' => 'user_profile_pictures',
    'languages' => array('en' => 'English', 'fr'=>'French','ge'=>'German'),
    'gmail_username' => 'matchmaking@biopark.ee',
    'gmail_password' => 'Matchm2k1ng21',
    'routeAuthArray' => array(
        'virtual_events' => array('authid' => 'createevents' , 'pageid' => 'managevirtualevents'),
        'edit_virtual_event/{virtualeventid}' => array('authid' => 'createevents' , 'pageid' => 'createevents'),
        'countries' => array('authid' => 'masterdatamanagement' , 'pageid' => 'countries'),
        'cities' => array('authid' => 'masterdatamanagement' , 'pageid' => 'cities'),
        'cities/{countryid?}' => array('authid' => 'masterdatamanagement' , 'pageid' => 'cities'),
        'categories' => array('authid' => 'masterdatamanagement' , 'pageid' => 'categories'),
        'subcategories' => array('authid' => 'masterdatamanagement' , 'pageid' => 'subcategories'),
        'subcategories/{categoryid?}' => array('authid' => 'masterdatamanagement' , 'pageid' => 'subcategories'),
        
        
    )
]
  
?>