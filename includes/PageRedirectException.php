<?php
namespace videoViewer;
/**
 * An exception indicating the page needs to be redirected
 *
 * @author John Judy <john.a.judy@gmail.com>
 */
class PageRedirectException extends \Exception{
    /**
     * The HTTP status code to return to the user
     * @var int
     */
    protected $httpCode;
    
    /**
     * The redirect location to send the user with 30x series codes
     * @var string
     */
    protected $location='';
    
    public function __construct($httpCode,$location='',$previous=null){
        $this->httpCode = $httpCode;
        $this->location=$location;
        parent::__construct('Page returned HTTP code',$httpCode,$previous);
    }
    
    public function sendHeader(){
        if($this->httpCode<300){return;}//no status issue to send to client
        elseif($this->httpCode<400){//redirects
            header('Location: '.$this->location,true,$this->httpCode);
            return;
        }
        elseif($this->httpCode<500){//request errors
            switch($this->httpCode){
                case 400:
                    header('HTTP/1.0 400 Bad Request',true,400);
                    break;
                case 401:
                    header('HTTP/1.0 401 Unauthorized',true,401);
                    break;
                case 403:
                    header('HTTP/1.0 403 Forbidden',true,403);
                    break;
                case 404:
                    header('HTTP/1.0 404 Not Found',true,404);
                    break;
                case 409:
                    header('HTTP/1.0 409 Conflict',true,409);
                    break;
                default:
                    header('HTTP/1.0 '.$this->httpCode,true,$this->httpCode);
            }
            return;
        }
        else{//server errors (50x)
            switch($this->httpCode){
                case 500:
                    header('HTTP/1.0 500 Internal Server Error',true,500);
                    break;
                case 501:
                    header('HTTP/1.0 501 Not Implemented',true,501);
                    break;
                case 503:
                    header('HTTP/1.0 503 Service Unavailable',true,503);
                    break;
            }
            return;
        }
    }
}

?>
