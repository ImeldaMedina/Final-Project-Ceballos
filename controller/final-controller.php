<?php
class FinalController
{
    private $_f3; //router
    private $_val; //validation
    private $_db; // database


    public function __construct($f3, $db)
    {
        $this->_f3 = $f3;
        $this->_db = $db;
        $this->_val = new FinalValidation($f3);
    }

    public function home()
    {
        $view = new Template();
        echo $view->render('views/home.html');
    }
    public function newUser()
    {
        if (isset($_SESSION['username'])) {
            echo "<script> confirm('you are already logged in!') </script>";
            $this->_f3->reroute($this->_f3->get('SERVER.HTTP_REFERER'));
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {


            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password= $_POST['password'];

            $newUser = new NewUser($fname, $lname, $email, $username, $password);

            //echo $_SESSION['username'];

            if ($this->_val->validForm()) { // if validated

                //$this->_db->newUser($newUser);

                //echo 'no';
                $this->_f3->reroute('/login');




            } else {
                //Store login name in a session variable
                $this->_f3->set('newUser', $_POST);
            }
        }

        $view = new Template();
        echo $view->render('views/new-user.html');
    }
    public function login()
    {

        if (isset($_SESSION['username'])) {
            $this->_f3->reroute('/home');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {

            $username = $_POST['username'];
            $password= $_POST['password'];

            //echo $_SESSION['username'];

            $id = $this->_db->validateLogin($username, $password)['id'];

            if (!$id) { // if not validated
                //echo 'no';
            } else {
                $_SESSION['username'] = $username;

                $name = $this->_db->getName($id);
                $name = $name['fname'].' '.$name['lname'];
                $_SESSION['name'] = $name;


                $this->_f3->reroute('/home');
                //Redirect to page 1
            }
        }
        $view = new Template();
        echo $view->render('views/login.html');
    }

    public function customShip()
    {

        if (!isset($_SESSION['username'])) { // must be logged in
            $this->_f3->reroute('/login');
            echo "<script type='text/javascript'>alert('You must be logged in to place an order');</script>";
        }

        //If form has been submitted, validate
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Get the data from the form
            $purpose = $_POST['purpose'];
            $color= $_POST['color'];
            $shielding = $_POST['shielding'];
            $generator = $_POST['generator'];
            $engine = $_POST['engine'];
            $hyperdrive = $_POST['hyperdrive'];

            if($purpose == 'p-0001'){
                $ship = new StarShip('testName', $generator, $engine, $hyperdrive, $shielding, $color);
            } else if ($purpose = 'p-0002') {
                $ship = new BattleShip('testName', $generator, $engine, $hyperdrive, $shielding, $color);
            } else if ($purpose = 'p-0003'){
                $ship = new Liner('testName', $generator, $engine, $hyperdrive, $shielding, $color);
            } else if ($purpose = 'p-0004'){
                $ship = new Yacht('testName', $generator, $engine, $hyperdrive, $shielding, $color);
            } else { // if invalid ship type
                $this->_f3->reroute('/finalize');
            }

            //Add data to hive

            $this->_f3->set('ship', $ship);


            //If data is valid
            if(true /*$this->_val->validFormCustomize() */ ){

                //write data to session

                $_SESSION['ship'] = $ship;


                $_SESSION['purp'] = $purpose;
                $_SESSION['col'] = $color;
                $_SESSION['shield'] =$shielding;
                $_SESSION['gen'] =$generator;
                $_SESSION['eng'] =$engine;
                $_SESSION['hyper'] =$hyperdrive;

                //redirect to finalize
/*
                if(is_a($ship, 'BattleShip')) {
                    $this->_f3->reroute('/finalize');
                }
                if(is_a($ship, 'Liner')) {
                    $this->_f3->reroute('/finalize');
                }
                if(is_a($ship, 'Yacht')) {
                    $this->_f3->reroute('/finalize');
                }*/



                $this->_f3->reroute('/summary');
            }
        }
        $view = new Template();
        echo $view->render('views/customize.html');
    }

    public function summary()
    {

        $ship = $_SESSION['ship'];

        echo '<br>';
        echo '<br>';
        echo '<br>';
        //echo '<br>';
        //var_dump($ship);

        //var_dump($ship->getHyperdrive());
        //echo 'test';

        //$this->_f3->set('purp',$purpose);
        $this->_f3->set('col',$ship->getColor());
        $this->_f3->set('shield',$ship->getShield());
        $this->_f3->set('gen',$ship->getGenerator());
        $this->_f3->set('eng',$ship->getEngine());
        $this->_f3->set('hyper',$ship->getHyperdrive());

        if (!isset($_SESSION['username'])) { // must be logged in
            $this->_f3->reroute('/login');
            echo "<script type='text/javascript'>alert('You must be logged in to place an order');</script>";
        }

        $view = new Template();
        echo $view->render('views/summary.html');
    }

    public function logout()
    {
        //this will wipe everything
        $_SESSION = array();
        //session_destroy();

        $view = new Template();
        echo $view->render('views/home.html');

        echo "<script type='text/javascript'>alert('you have been logged out');</script>";
    }



}
