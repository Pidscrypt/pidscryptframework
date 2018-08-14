<?php
    namespace Errors;

    use Engine\Template;

    //use Redirect;

    class Error {
        private $message = null,
                $notice = null,
                $error_number = null;

        public function __construct($num = null,$note = null, $msg = null){
            $this->message = $msg;
            $this->notice = $note;
            $this->error_number = $num;
        }

        public function getMessage(){
            return $this->message;
        }
        public function getNumber(){
            return $this->error_number;
        }
        public function getNotice(){
            return $this->notice;
        }

        public function getErrorPage(){
            $output = "";
            if(!$this->message){
                $this->message = "
                It's looking like you may have taken a wrong turn. Don't worry... it happens to
                the best of us. You might want to check your internet connection. Here's a little tip that might
                help you get back on track.";
            }

            $error_page = new Template(DOCUMENT_ROOT."/views/404.tpl");
            $error_page->set("error_message",$this->getMessage());
            $error_page->set("error_notice",$this->getNotice());
            $error_page->set("error_number",$this->getNumber());
            return $error_page->output();
        }
    }
?>