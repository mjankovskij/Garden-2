<?php

class Cucumber
{
    // private $id;
    // private $img;
    // private $count;

    public function __construct($dbhost = 'localhost', $dbuser = 'root', $dbpass = '', $dbname = 'sodas', $charset = 'utf8')
    {
        $this->connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        $this->connection->set_charset($charset);
        $this->url = explode('/', $_SERVER['REQUEST_URI']);
        $this->link = 'http://' . $_SERVER['SERVER_NAME'] . $this->url[0] . '/' . $this->url[1] . '/' . $this->url[2] . '/' . $this->url[3];
    }

    public function renderGarden()
    {
        $sql = $this->connection->query('SELECT * FROM garden ORDER by id');
        while ($row = $sql->fetch_assoc()) {
            $id = $row['id'];
            $img = $row['img'];
            $count = $row['count'];

            echo "<div class='garden'>
            <div class='data'><img src='" . $this->link . "/img/a$img.jpg' alt='agurkai'></div>
            <div class='data'>$id</div> 
            <div class='data'>$count</div> 
            <div class='data close'>
            <a href='" . $this->link . "/garden/uproot/$id'>X</a>
            </div>
            </div>";
        }
        echo '<form action="' . $this->link . '/' . $this->url[4] . '/growNew" method="POST">
        <button type="submit">Pasodinti nauja agurka</button></form>';
    }

    public function uproot($id)
    {
        $this->connection->query("DELETE FROM garden WHERE id='$id'");
    }

    public function growNew()
    {
        $rand = rand(1, 3);
        $this->connection->multi_query("INSERT INTO garden (img , count) VALUES ('$rand', '0');");
    }

    public function renderGrow()
    {
        echo '<form action="' . $this->link . '/' . $this->url[4] . '/growAll" method="POST">';
        $sql = $this->connection->query('SELECT * FROM garden ORDER by id');
        while ($row = $sql->fetch_assoc()) {
            $id = $row['id'];
            $img = $row['img'];
            $count = $row['count'];
            $rand = rand(1, 10);
            echo "<div class='growing'>
            <div class='data'><img src='" . $this->link . "/img/a$img.jpg' alt='agurkai'></div>
            <div class='data'>$id</div> 
            <div class='data'>$count</div> 
            <div class='data'>$rand</div>
            </div>
            <input type='hidden' name='$id' value='$rand'>";
        }
        echo '<button type="submit">Auginti</button></form>';
    }

    public function growAll($data)
    {
        foreach ($data as $key => $item) {
            $this->connection->query("UPDATE garden SET count = count + $item WHERE id = '$key'");
        }
    }

    public function renderPick()
    {
        $sql = $this->connection->query('SELECT * FROM garden ORDER by id');
        while ($row = $sql->fetch_assoc()) {
            $id = $row['id'];
            $img = $row['img'];
            $count = $row['count'];

            echo "<div class='pick'>
            <div class='data'><img src='" . $this->link . "/img/a$img.jpg' alt='agurkai'></div>
            <div class='data'>$id</div> 
            <div class='data'>$count</div> 
            <div class='data'>
            <form action='" . $this->link . '/' . $this->url[4] . "/pickCucumbers' method='POST'>
            <input type='text' name='count'>
            </div>
            <div class='data'>
            <button type='submit' name='id' value='$id'>Skinti</button></form>
            </div>
            <div class='data'>
            <form action='" . $this->link . '/' . $this->url[4] . "/pickCucumbers' method='POST'>
            <input type='hidden' name='count' value='$count'>
            <button type='submit' name='id' value='$id' class='extra'>Skinti visus</button></form>
            </div>
            </div>";
        }
    }
    
    public function pick($id, $value)
    {
        if(!$id || !$value){
            ?><script>alert('Nuskinti nepavyko, prasome pasitikslinti skinama kieki.')</script><?php
            return 'Error';
        }
        
        $amount = preg_replace('/[^0-9]/', '', $value);
        
        if($amount!=$value || $amount<0){
            ?><script>alert('Prasome pasitikslinti skinama kieki.')</script><?php
            return 'Error';
        }
        
        $sql = $this->connection->query("SELECT count FROM garden WHERE id = '$id'");
        if(!($sql->num_rows)){
            ?><script>alert('Tokiu agurku nera.')</script><?php
            return 'Error';
        }
        
        while ($row = $sql->fetch_assoc()) $u_amount = $row["count"];
        
        if($u_amount-$amount<0){
            ?><script>alert('Kiekis negali buti neigiamas.')</script><?php
            return 'Error';
        }
        $this->connection->query("UPDATE garden SET count = count - $amount WHERE id = '$id'");
        return 'OK';
    }

}
