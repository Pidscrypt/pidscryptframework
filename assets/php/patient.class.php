<?php
    class Patient {
        private $_db;
        private $_data;
        private $_all;
        private $_isInPatient;

        public function get_patient_name(){
            return ucfirst($this->data()->patient_fname." ".$this->data()->patient_lname." ".$this->data()->patient_oname);
        }

        public function __construct($patient = null){
            $this->_db = Database::getInstance();

            if(!$patient){
                /** @TODO:: process patient */
                //$this->findAll();
            }else{
                $this->find($patient);
            }
        }

        public function add($fields = array()){
            if(!$this->_db->insert("patients",$fields)){
                throw new Exception('There was a problem adding patient.');
            }
        }

        public function data(){
            return $this->_data;
        }

        public function find($patient = null){
            if($patient){
                $field = (is_numeric($patient))?'patient_id':'patient_fname';
                $data = $this->_db->get('patients', array($field, '=', $patient));
                //exit(var_dump($data));
                if($data->count()){
                    $this->_data = $data->first();
                    return true;
                }
            }
            return false;
        }

        public function exists(){
            return (!empty($this->data()))?true:false;
        }

        public function isInPatient(){
            return $this->_isInPatient;
        }

        public function update($fields = array(), $patient_id = null){

            if(!$patient_id && $this->_isInPatient()){
                $patient_id = $this->data()->patient_id;
            }

            try{
                if(!$this->_db->update('patients', $patient_id, $fields)){
                    throw new Exception('there was a problem updating patient');
                }
            }catch(Exception $ex){
                die($ex->getMessage());
            }
        }

        public function tabulate($title, $where){
            $patient = $this->_db->get('patients', $where);
            //Table::draw($title, $table);
            if(!$patient->count()){
                echo "<div class='container'><h3>No {$title} yet.</h3></div>";
            }else{
                
            echo "<h3>".$title."</h3>";
            $output = <<<HTML
            <div class="table-responsive table-bordered">
                <table class="table" >
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Queue</th>
                        </tr>
                    </thead>
                    <tbody>
HTML;
                foreach($patient->results()  as $patient){
                    $output .= "<tr><td>".$patient->patient_fname." ".$patient->patient_lname."</td><td>".$patient->admit_date."</td><td>".$patient->doctor_id."</td></tr>";
                }

                echo $output."</tbody></table></div>";
            }
        }

        public function patientFileUpdate($fields = array(), $file_id){

            try{
                if(!$this->_db->update('visits', 'visit_Id', $file_id, $fields)){
                    throw new Exception('there was a problem updating patient file');
                }
            }catch(Exception $ex){
                die($ex->getMessage());
            }
        }
    }
?>
