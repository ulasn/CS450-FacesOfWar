<?php

$conn = new mysqli("localhost","root","","fow");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->set_charset("utf8");

$query ="Select * from novel";
$result = $conn->query($query);

$novels = array();

while($row = $result->fetch_row())
{
    $novels[] = array("id" => $row[0], "author" => $row[1], "name" => $row[2], "year" => $row[3], "nationality" => $row[4], "details" => $row[5]);
}

$query ="Select * from poems";
$result = $conn->query($query);

$poems = array();

while($row = $result->fetch_row())
{
    $poems[] = array("id" => $row[0], "poet" => $row[1], "poem" => $row[2], "year" => $row[3], "nationality" => $row[4], "details" => $row[5]);
}

?>



<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<head>
<title>Faces Of War</title>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> 
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">

<style>
    .card {
    max-width: 1650px!important;
}
    #borderless{
        border:0px;
    }
    
    div#content{
        background : #575D62;
    }
    </style>

</head>

<body>
   
  
   

  
    
<div id="sidebar">
  <br>
  <div class="facesofwar">
      <a href="index.php">FACES<br/><tag style="font-size:38px">OF</tag><br/>WAR</a>
   </div>
   
    <ul>
        <li>
            <h4><a href="index.php">CASUALITIES</a></h4>
        </li>
        <br>
        <li>
            <h4><a href="propaganda.php">PROPAGANDA</a></h4>
        </li>
        <br>
        <li>
            <h4><a href="literature.php">LITERATURE</a></h4>
        </li>
        <br>
        <li>
            <h4><a href="#">ABOUT</a></h4>
        </li>
    </ul>
   


    
</div>      
        
            
<div id="content">


 
<div class="container">
   
    <div id="borderless" class="card text-white bg-dark mb-3">
      <br>
      <div class="card-body">
       
       <h1>&nbsp;&nbsp;Novels</h1><br>
        <?php
    
    foreach($novels as $novel){
        echo'<div class="card text-white bg-dark mb-3" style="max-width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">'. $novel['author'] . ' - ' . $novel['name'] . '<div align="right">' . '<h6>'  . $novel['year'] . '</h6></div></h5>
    <p class="card-text">' . $novel['details'] . '</p></div></div>';
    }
    
    
    ?>
      
  
    </div>
    
    
    </div> 
    

     
     <!--<div id="borderless" class="card text-white bg-dark mb-3">
      <br>
      <div class="card-body">
       
             <h1>&nbsp;&nbsp;Poems</h1><br> 
        <?php
    
   /* foreach($poems as $poem){
        echo'<div class="card text-white bg-dark mb-3" style="max-width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">'. $poem['poet'] . ' - ' . $poem['poem'] . '<div align="right">' . '<h6>'  . $poem['year'] . '</h6></div></h5>
    <p class="card-text">' . $poem['details'] . '</p></div></div>';
    }*/
    
    
    ?>
      
  
    </div>
    
    
    </div>--> 
</div>      
</div>
    
   
  
 



</body>
</html>