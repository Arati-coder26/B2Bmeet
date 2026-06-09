<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function convert_digit_to_words($no)  
    {   
    
    //creating array  of word for each digit
     $words = array('0'=> 'Zero' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five','6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten','11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fourteen','15' => 'fifteen','16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty','30' => 'thirty','40' => 'forty','50' => 'fifty','60' => 'sixty','70' => 'seventy','80' => 'eighty','90' => 'ninty','100' => 'hundred','1000' => 'thousand','100000' => 'lakh','10000000' => 'crore');
     $numbers = array('0'=> '0' ,'1'=> '1' ,'2'=> '2' ,'3' => '3','4' => '4','5' => '5','6' => '6','7' => '7','8' => '8','9' => '9','10' => '10','11' => '11','12' => '12','13' => '13','14' => '14','15' => '15','16' => '16','17' => '17','18' => '18','19' => '19','20' => '20','30' => '30','40' => '40','50' => '50','60' => '60','70' => '70','80' => '80','90' => '90','100' => '100','1000' => '1000','100000' => '100000','10000000' => '10000000');
     
     
     //for decimal number taking decimal part
     
    $cash=(int)$no;  //take number wihout decimal
    $decpart = $no - $cash; //get decimal part of number
    
    $decpart=sprintf("%01.2f",$decpart); //take only two digit after decimal
    
    $decpart1=substr($decpart,2,1); //take first digit after decimal
    $decpart2=substr($decpart,3,1);   //take second digit after decimal  
    
    $decimalstr='';
    
    //if given no. is decimal than  preparing string for decimal digit's word
    
    if($decpart>0)
    {
        $firstPartAfterDecimal = intval($decpart1)*10;
        $firstPartAfterDecimal = strval($firstPartAfterDecimal);
        $firstPart = ($words[$firstPartAfterDecimal]!='Zero'    ?   ($words[$firstPartAfterDecimal]) : '');
        $secondPart = ($words[$decpart2]!='Zero'    ?   ($words[$decpart2]) : '');
     $decimalstr.=" rupees ".$firstPart." ".$secondPart.' paise ';
    }
     
        if($no == 0)
            return ' ';
        else {
        $novalue='';
        $highno=$no;
        $remainno=0;
        $value=100;
        $value1=1000;       
                while($no>=100)    {
                    if(($value <= $no) &&($no  < $value1))    {
                    $novalue=$words["$value"];
                    $highno = (int)($no/$value);
                    $remainno = $no % $value;
                    break;
                    }
                    $value= $value1;
                    $value1 = $value * 100;
                }       
              if(array_key_exists("$highno",$words))  //check if $high value is in $words array
                  return $words["$highno"]." ".$novalue." ".convert_digit_to_words($remainno).$decimalstr;  //recursion
              else {
                 $unit=$highno%10;
                 $ten =(int)($highno/10)*10;
                 return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".convert_digit_to_words($remainno
                 ).$decimalstr; //recursion
               }
        }
    }

function convert_number_to_words($number) {

  $no = round($number);
   $point = round($number - $no, 2) * 100;

   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? '' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);

  $second = (isset($words[$point / 10])) ? ( $words[$point / 10]    )   :   ( "");
  $third = (isset($words[$point = $point % 10])) ? ( $words[$point = $point % 10]    )   :   ( "");
  $points = ($point) ?
    "," . $second . " " . 
          $third : '';
   return ( $result . "Rupees  " . $points . " Paise");




    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        100000             	=> 'lakh',
        10000000          	=> 'crore',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= ' ' . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function random_generator($digits,$alphanumeric='alphanumeric'){

srand ((double) microtime() * 10000000);
//Array of alphabets
if($alphanumeric=='numeric') $input=range(0,9);
else if($alphanumeric=='alphabets') $input=range('A','Z');
else $input=array_merge(range('A', 'Z'), range(1, 10)); 

$random_generator="";// Initialize the string to store random numbers
for($i=1;$i<($digits+1);$i++){ // Loop the number of times of required digits

if(rand(1,2) == 1){// to decide the digit should be numeric or alphabet
// Add one random alphabet 
$rand_index = array_rand($input);
$random_generator .=$input[$rand_index]; // One char is added

}else{

// Add one numeric digit between 1 and 10
$random_generator .=rand(1,10); // one number is added
} // end of if else

} // end of for loop 
$random_generator=substr($random_generator,0,$digits);
return $random_generator;
} // end of function

function substring_of_string_check($superstring,$substring){
	if (strpos($superstring,$substring) !== false) {   return TRUE;}
	return FALSE;

}
function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}
function xml2array($xml)

{

	$arr = array();

	foreach ($xml->children() as $r)

	{

		$t = array();

		if (count($r->children()) == 0)

		{

			$arr[$r->getName()] = strval($r);

		}

		else

		{

			$arr[$r->getName()][] = xml2array($r);

		}

	}

	return $arr;

}
function convertDateFormat($fromFormat,$toFormat,$dateinput,&$dob)
{
	try {
		$date = DateTime::createFromFormat($fromFormat, $dateinput);
		$dob= $date->format($toFormat);
		return "success";
	}
	
	//catch exception
	catch(Exception $e) {
		$dob="";
		return $e->getMessage();
	}
	
	
	
}
function closetags ( $html )
{
	#put all opened tags into an array
	preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
	$openedtags = $result[1];
	#put all closed tags into an array
	preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
	$closedtags = $result[1];
	$len_opened = count ( $openedtags );
	# all tags are closed
	if( count ( $closedtags ) == $len_opened )
	{
		return $html;
	}
	$openedtags = array_reverse ( $openedtags );
	# close tags
	for( $i = 0; $i < $len_opened; $i++ )
	{
		if ( !in_array ( $openedtags[$i], $closedtags ) )
		{
			$html .= "</" . $openedtags[$i] . ">";
		}
		else
		{
			unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
		}
	}
	return $html;
}
function removeSpecialCharacters($input){
    $input  = str_replace("\\", "", $input);
    $input  = str_replace("\"", "", $input);
    $input  = str_replace("{PRE}", "", $input);
    return $input ;
}
function custom_file_exists($filePath)
    {
          if((is_file($filePath))&&(file_exists($filePath))){
            return true;
          }   
          return false;
    }


    function sendHtmlEmail($mailObj){
        
        require __DIR__.'/../../vendor/autoload.php';
        
        // SMTP configuration
        $mail = new PHPMailer(); // create a new object

        $mail->SMTPDebug = 2; //Alternative to above constant
        
        /* $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = (config('global.gmail_username'));
        $mail->Password = (config('global.gmail_password'));
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        */
        //$mail->Host = 'smtp.gmail.com';
        

        $mail->isSMTP();
        //$mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.zone.eu';
        $mail->SMTPAuth = true;
        $mail->Username = (config("global.gmail_username"));
        $mail->Password = (config("global.gmail_password"));
        
        $mail->SMTPSecure = 'TLS';
        // $mail->Port = 465; // for europe email
        $mail->Port = 587;
        

        
        $mail->setFrom((config('global.gmail_username')), config('global.site_name'));
        
        // Add a recipient
        
        
        // Email subject
        $mail->Subject = $mailObj['subject'];
        
        $body = $mailObj['htmlcontent'];
        
        $mail->addAddress($mailObj['to_address'],$mailObj['to_name']);

        $mail->Body = $body;
        
        // Set email format to HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug  = 0; 

        // if(1){
        if(!$mail->send()){
            // echo 'Message could not be sent.';
            $error =  'Mailer Error: ' . $mail->ErrorInfo;
        }else{
            //          echo 'Message has been sent';
        }
    }
?>