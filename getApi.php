<?php 
    ob_start();
    /* 
        This is class script proceeding secured API
        To use this class you should keep same as query string and function name
    */

    require_once("Init.php");
    require_once("Rest.inc.php");

    class API extends REST
    {
        private $db, $obj;

        public function __construct()
        {
            parent::__construct();        // Init parent contructor
        }   

        /* 
         *  Simple login API
         *  Login must be POST method
         *  email : <USER EMAIL>
         *  pwd : <USER PASSWORD>
         */
        
        




    public function GetUsersById()
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }        

        $condition = ""; $orcondition = "";

        if(isset($_POST['id']) && $_POST['id']!=NULL){
            $id = $_POST['id'];
            $condition.=empty($condition)?" u.id='$id' ":" AND u.id='$id' ";
        }
                    
        if(!empty($_POST['userid'])){
            $uid = $_POST['userid'];
            $condition.=empty($condition)?" ud.uid='$uid' ":" AND ud.uid='$uid' ";
        }

        if(!empty($_POST['urn'])){
            $urn = $_POST['urn'];
            $condition.=empty($condition)?" u.urn='$urn' ":" AND u.urn='$urn' ";
        }

        if(isset($_POST['usertype']) && $_POST['usertype']!=NULL){
            $usertype = $_POST['usertype'];
            $condition.=empty($condition)?" u.usertype='$usertype' ":" AND u.usertype='$usertype' ";
        }

        if(!empty($_POST['forcenumber'])){
            $forceno = $_POST['forcenumber'];
            $condition.=empty($condition)?" u.forceno='$forceno' ":" AND u.forceno='$forceno' ";
        }

        if(!empty($_POST['emailid'])){
            $emailid = $_POST['emailid'];
            $condition.=empty($condition)?" ud.emailid='$emailid' ":" AND ud.emailid='$emailid' ";
        }

        if(!empty($_POST['pannumber'])){
            $pan_no = $_POST['pannumber'];
            $condition.=empty($condition)?" ud.pan_no='$pan_no' ":" AND ud.pan_no='$pan_no' ";
        }

        if(!empty($_POST['mobile'])){
            $mobile = $_POST['mobile'];
            $orcondition.=empty($condition)?" (ud.mobile='$mobile' OR ud.alt_mobile = '$mobile' OR ud.whatsapp_number = '$mobile') AND ":" (ud.mobile='$mobile' OR ud.alt_mobile = '$mobile' OR ud.whatsapp_number = '$mobile') AND ";
        }

        if(!empty($_POST['fname'])){
            $fname = $_POST['fname'];
            $condition.=empty($condition)?" ud.fname='$fname' ":" AND ud.fname='$fname' ";
        }

        if(!empty($_POST['lname'])){
            $lname = $_POST['lname'];
            $condition.=empty($condition)?" ud.lname='$lname' ":" AND ud.lname='$lname' ";
        }
        
        if(isset($_POST['isactive']) && $_POST['isactive']!=NULL){
            $isactive = $_POST['isactive'];
            $condition.=empty($condition)?" u.isactive='$isactive' ":" AND u.isactive='$isactive' ";
        }

        if(isset($_POST['publishdate']) && $_POST['publishdate']!=NULL){
            $publishdate = $_POST['publishdate'];
            $condition.=empty($condition)?" u.publishdate='$publishdate' ":" AND u.publishdate='$publishdate' ";
        }

        if(isset($_POST['locationpreference1']) && $_POST['locationpreference1']!=NULL){
            $location_preference1 = $_POST['locationpreference1'];
            $condition.=empty($condition)?" ud.location_preference1='$location_preference1' ":" AND ud.location_preference1='$location_preference1' ";
        }
        if(isset($_POST['locationpreference2']) && $_POST['locationpreference2']!=NULL){
            $location_preference2 = $_POST['locationpreference2'];
            $condition.=empty($condition)?" ud.location_preference2='$location_preference2' ":" AND ud.location_preference2='$location_preference2' ";
        }

        $condition = trim(str_replace(' ', ' ', $condition));
        $query = (!empty($condition))?
            "SELECT u.id,u.urn,u.usertype,u.refrenceby,u.dob,u.forceno,ud.fname,ud.lname,ud.mobile,u.isactive,u.publishdate FROM users as u join user_details as ud on u.id=ud.uid WHERE $orcondition $condition ORDER BY u.publishdate ASC":
            "SELECT u.id,u.urn,u.usertype,u.refrenceby,u.dob,u.forceno,ud.fname,ud.lname,ud.mobile,u.isactive,u.publishdate FROM users as u join user_details as ud on u.id=ud.uid ORDER BY u.id DESC";

        $result = $result = $this->db->read($query);
        if (count($result) > 0) {
            $message = array('status' => "1", "msg" => "Got List Successfully");
            $posts_data = array_merge($result, $message);
            ($_REQUEST['format'] == 'xml') ? $this->response($this->xml2($posts_data), 200) : $this->response($this->json($posts_data), 200);

        } else {
            $error = array('status' => "0", "msg" => "Not Any user profile Found !!");
            ($_REQUEST['format'] == 'xml') ? $this->response($this->xmlStatus($error), 400) : $this->response($this->json($error), 400);
        }
        
    }

   

}

// Initiiate Library
$api = new API;
$api->processApi();
ob_end_flush(); ?>