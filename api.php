<?php ob_start();
    /* 
     * This is class script proceeding secured API
     * To use this class you should keep same as query string and function name
    */
    require_once("connect/Db.class.php");
    require_once("Rest.inc.php");
    require_once ("connect/CRUD/Tables.class.php");
    
    class API extends REST
    {

        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        private $_db;
        public function __construct($data = array())
        {
           $this->_db = new DB();
        }
        public function processApi()
        {

            $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));

            if ((int)method_exists($this, $func) > 0)
                $this->$func();
            else
                $this->response('', 404);// If the method not exist with in this class, response would be "Page not found".
        }
    
        public function publicjson($data)
        {
            return $this->json($data);
        }

        public function publicxml($posts)
        {
            return $this->xml($posts);
        }

        private function json($data)
        {
            header('Content-type: application/json');
            return json_encode(array('posts' => $data));
        }

        private function xmlStatus($posts)
        {
            if (is_array($posts)) {
                $data = '';
                header('Content-type: text/xml');
                echo '<posts>';
                foreach ($posts as $tag => $val) {
                    echo '<', $tag, '>', htmlentities($val), '</', $tag, '>';
                }
                echo '</posts>';
            }
        }

        private function xml($posts)
        {
            if (is_array($posts)) {
                $data = '';
                header('Content-type: text/xml');
                echo '<posts>';
                foreach ($posts as $index => $post) {
                    if (!is_array($post)) {
                        echo '<', $index, '>', htmlentities($post), '</', $index, '>';
                    } else if (is_array($post)) {
                        foreach ($post as $key => $value) {
                            echo '<', $key, '>';
                            if (!is_array($value)) {
                                //echo '<',$key,'>',htmlentities($value),'</',$key,'>';
                                echo htmlentities($value);
                            } else if (is_array($value)) {
                                foreach ($value as $tag => $val) {
                                    echo '<', $tag, '>', htmlentities($val), '</', $tag, '>';
                                }
                            }
                            echo '</', $key, '>';
                        }
                    }

                }
                echo '</posts>';
            }
        }

        ////////////////////////////////////function xml2 is deal with the worng xml formats//////
        private function xml2($posts)
        {
            if (is_array($posts)) {
                $data = '';
                header('Content-type: text/xml');
                echo '<posts>';
                foreach ($posts as $index => $post) {
                    echo '<post>';
                    if (!is_array($post)) {
                        echo '<', $index, '>', htmlentities($post), '</', $index, '>';
                    } else if (is_array($post)) {
                        foreach ($post as $key => $value) {
                            echo '<', $key, '>';
                            if (!is_array($value)) {
                                //echo '<',$key,'>',htmlentities($value),'</',$key,'>';
                                echo htmlentities($value);
                            } else if (is_array($value)) {

                                foreach ($value as $tag => $val) {
                                    echo '<', $tag, '>', htmlentities($val), '</', $tag, '>';
                                }
                            }
                            echo '</', $key, '>';
                        }
                    }
                    echo '</post>';
                }
                echo '</posts>';
            }
        }
        
        private function persons(){            
            switch($this->get_request_method()){
                case "GET":
                    $condition = NULL;
                    
                    $req = &$_GET; //print_r($req);
                    unset($req['rquest']);
                    
                    $arr = $req;
                    
                    foreach ($arr as $key => $value) {
                        $this->_db->bind($key,$value);
                        $condition.= empty($condition)?"$key = :$key":" AND $key = :$key";
                    }
                    
                    $sql = !empty($condition)
                            ?"SELECT * FROM `persons` WHERE $condition"
                            :"SELECT * FROM `persons`";
                    
                    $result = $this->_db->query($sql);
            
                    if(!empty($result)){
                        $this->response($this->json($result), 200);
                    }
                    $this->response('',204);	// If no records "No Content" status
                    
                    break;
                case "POST":                    
                    $req = &$_POST; print_r($req);
                    unset($req['rquest']);
                    $arr = $req;
                    $person  = new Person();
                    foreach ($arr as $key => $value) {
                        $person->$key = $value;
                    }
                    
                    $result = $person->search();
                    if(!empty($result)){
                        $this->response($this->json($result), 200);
                    }
                    $this->response('',204);
                    break;
                case "PUT":
                    break;
                case "DELETE":
                    break;
                default:
                    $this->response('',406);
                    break;
            }
            
        }
        
        private function get(){
            $headers = apache_request_headers();
            $class = NULL;
            
            //Check whether "Resource" is not defined in the header request
            if(isset($headers['Resource'])){
                //Decode "Resource" header to get the class name
                $class = base64_decode($headers['Resource']);
            }else{
                //Return "400::Bad Request"
                $this->response($this->json(''),400);
            }
            
            //If Class Name does not exists return "405::Method Not Allowed"
            if(!class_exists($class)){
                $this->response($this->json(''),405);
            }
            
            //Switch operation based on Request Method (GET/POST/PUT/DELETE)
            switch($this->get_request_method()){
                case "GET": //OPERATION: Fetch / Search / Find / Avg / Sum / Min / Max, etc.     
                    //Retrieve Parameters send using GET Request
                    $param = &$_GET;
                    
                    //Unset "rquest" Parameter (The Parameter used to call desired function)
                    unset($param['rquest']);
                    
                    //Creating Object of Class as per "Resource" Header passed
                    $resource = new $class();
                    
                    //Defining Resource Keys used for WHERE clause / CONDITIONS
                    foreach ($param as $key => $value) {
                        $resource->$key = $value;
                    }
                    
                    //Searching records based on above keys/parameters
                    $result = $resource->search();
                    
                    //Return Result JSON with "200::Success"
                    if(!empty($result)){
                        $this->response($this->json($result), 200);
                    }
                    
                    //Return "204::No Content" if Result=NULL
                    $this->response($this->json(''),204);
                    break;
                case "POST": //ALLOWED OPERATION: Create()    
                    //Retrieve Parameters send using POST Request
                    $param = &$_POST;
                    
                    //Unset "rquest" Parameter (The Parameter used to call desired function)
                    unset($param['rquest']);
                    
                    //Creating Object of Class as per "Resource" Header passed
                    $resource = new $class();
                    
                    //Defining Resource Keys used for WHERE clause / CONDITIONS
                    foreach ($param as $key => $value) {
                        $resource->$key = $value;
                    }
                    
                    //Searching records based on above keys/parameters
                    $result = $resource->search();
                    
                    //Return Result JSON with "200::Success"
                    if(!empty($result)){
                        $this->response($this->json($result), 200);
                    }
                    
                    //Return "204::No Content" if Result=NULL
                    $this->response($this->json(''),204);
                    break;
                case "PUT":
                    break;
                case "DELETE":
                    break;
                default:
                    $this->response($this->json(''),406);
                    break;
            }
            
        }
        
        private function set(){
            //ALLOWED METHOD: POST
            if($this->get_request_method()==="POST"){
                //Fetch REQUEST HEADERS
                $headers = apache_request_headers();
                
                $class = NULL;
                //Check whether "Resource" is not defined in the header request
                if(isset($headers['Resource'])){
                    //Decode "Resource" header to get the class name
                    $class = base64_decode($headers['Resource']);
                }else{
                    //Return "400::Bad Request"
                    $this->response($this->json(''),400);
                }

                //If Class Name does not exists return "405::Method Not Allowed"
                if(!class_exists($class)){
                    $this->response($this->json(''),405);
                }
                
                //Retrieve Parameters send using GET Request
                $param = &$_POST;

                //Unset "rquest" Parameter (The Parameter used to call desired function)
                unset($param['rquest']);

                //Creating Object of Class as per "Resource" Header passed
                $resource = new $class();

                //Defining Resource Keys used for WHERE clause / CONDITIONS
                foreach ($param as $key => $value) {
                    $resource->$key = $value;
                }

                //Searching records based on above keys/parameters
                $result = $resource->create();

                //Return Result JSON with "200::Success"
                if(!empty($result)){
                    $this->response($this->json($result), 200);
                }

                //Return "204::No Content" if Result=NULL
                $this->response($this->json(''),204);                
            }
            
            //Return 406 => 'Not Acceptable' if method not allowed
            $this->response($this->json(''),406);
        }
    }

    // Initiiate Library
    $api = new API;
    $api->processApi();
    ob_end_flush(); 
?>