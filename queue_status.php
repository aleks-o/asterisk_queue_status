<html>
<head>
<?php
  $queue=$_GET['queue'];
 echo "<meta http-equiv='refresh' content='3;url=queue_status.php?queue=$queue'>";
?>
 <title>Статистика очереди</title>

  <style type="text/css">
  td.large {
  color:red;
  text-align:center;
  font-size:36pt;
  }
  </style>

  <style type="text/css">
  td.medium {
  color:black;
  text-align:center;
  font-size:18pt;
  }
  </style>

  <style type="text/css">
  td.mediumq {
  color:black;
  background-color:#FA5858;
  text-align:center;
  font-size:18pt;
  }
  </style>


  <style type="text/css">
  tr.heading {
  color:blue;
  text-align:center;
  font-size:18pt;
  }
  </style>

  <style type="text/css">
  tr.heading-medium {
  color:blue;
  text-align:center;
  font-size:16pt;
  }
  </style>

</head>
 <body>

<?php
  $queue=$_GET['queue'];
//  require_once('./phpagi/phpagi-asmanager.php');
  require_once('/var/lib/asterisk/agi-bin/phpagi-asmanager.php');

$myfile = '/etc/asterisk/queues_additional.conf';
echo "<form id='queue' action='queue_status.php' method='GET'>";
$lines = file($myfile);
 echo "Выбор очереди - ";
foreach($lines as $queues){
 if (preg_match("/]/i", $queues)) {
 echo "<button name='queue' type='submit' value='".substr($queues, 1, -2)."'>".substr($queues, 1, -2)."</button>";
 }
}
echo "</form>";
echo "<br />";

  $asm = new AGI_AsteriskManager();
  if($asm->connect())
  {
    $result = $asm->Command("queue show $queue");

// COUNT AVAILABLE AGENTS

   $n = 0;

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/Local/i", $line)) {
          $n = $n + 1;
         }
      }
    }


// ECHO THE QUEUE STATUS FIRST

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading';><td>Номер очереди</td><td>Вызовов в очереди</td><td>Всего операторов</td><td>Обработано вызовов</td><td>Пропущено вызовов</td><td>Среднее время ожидания, сек</td><td>Среднее время разговора,сек</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/talktime/i", $line)) {
          echo "<tr>";
          $pieces = explode(" ", $line);
          echo "<td class='large';>$pieces[0] </td>";
          echo "<td class='large';>$pieces[2] </td> ";
          echo "<td class='large';>$n </td> ";
          echo "<td class='large';>".trim($pieces[14], "C:,")."</td> ";
          echo "<td class='large';>".trim($pieces[15], "A:,")."</td> ";
          echo "<td class='large';>".trim($pieces[9], "(s")." </td> ";
          echo "<td class='large';>".trim($pieces[11], "s")." </td> ";
          echo "</tr>";
         }
      }
      echo "</table>";
    }




// ECHO THE CALLS WAITING

   echo "<h3><u>Ожидающие в очереди</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
//      echo "<tr class='heading-medium';><td>Позиция</td><td>Время ожидания</td></tr>";
      echo "<tr class='heading-medium';>";
      echo "<td>№ - Время</td>";

      foreach(explode("\n", $result['data']) as $line)
      {

         if (preg_match("/wait/i", $line)) {
          $pieces2 = explode(" ", $line);
          //echo "<td>";
          //echo "<td class='medium';>".trim($pieces2[6], ".")." </td> ";
          //echo "<td class='medium';>".trim($pieces2[10], ",")." </td> ";
          echo "<td class='mediumq';>".trim($pieces2[6], ".")." - ".trim($pieces2[9], ",")." </td> ";
          //echo "<td class='medium';>".trim($pieces2[9], ",")." </td> ";
          //echo "</tr>";
      }

      }
      echo "</tr></table>";
    }



// Create 3 columns
echo "<br /><table align=center border='0'>";
echo "<tr><td width=600px align=left valign=top>";


// ECHO AGENTS RINGING

   echo "<h3><u>Вызываемые/Доступные операторы </u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading-medium';><td>Оператор</td><td>Последний вызов, сек</td><td>Вызовов сегодня</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/Ringing/i", $line)) {
          $pieces2 = explode(" ", $line);
          echo "<tr bgcolor=#FA5858>";
          echo "<td class='medium';> ".trim($pieces2[6], "Agent/")." </td> ";
          if(is_numeric($pieces2[20])) $c1 = $pieces2[20]; else $c1 = "?";
          if(is_numeric($pieces2[16])) $c2 = $pieces2[16]; else $c2 = "?";
          echo "<td class='medium';>$c1 </td> ";
          echo "<td class='medium';>$c2 </td> ";
          echo "</tr>";
         }
      }
//      echo "</table>";
    }



// ECHO AGENTS AVAILABLE

//   echo "<h3><u>Доступные операторы</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
//      echo "<table border='1'; cellpadding=6pt;>";
//      echo "<tr class='heading-medium';><td>Оператор</td><td>Последний вызов, сек</td><td>Вызовов сегодня</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/Not in use/i", $line)) {
          if (preg_match("/Local/i", $line)) {
           if (!preg_match("/paused/i", $line)) {
           $pieces2 = explode(" ", $line);
           echo "<tr bgcolor=#82FA58>";
           echo "<td class='medium';> ".trim($pieces2[6], "Agent/")." </td> ";
           if(is_numeric($pieces2[22])) $c1 = $pieces2[22]; else $c1 = "?";
           if(is_numeric($pieces2[18])) $c2 = $pieces2[18]; else $c2 = "?";
           echo "<td class='medium';>$c1 </td> ";
           echo "<td class='medium';>$c2 </td> ";
           echo "</tr>";
           }
          }
         }
      }
      echo "</table>";
    }



echo "</td><td width=600px valign=top>";


// ECHO AGENTS ON A CALL

   echo "<h3><u>Принявшие вызов/Занятые операторы</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading-medium';><td>Оператор</td><td>Последний вызов, сек</td><td>Вызовов сегодня</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/In call/i", $line)) {
          if (preg_match("/In use/i", $line)) {
          $pieces2 = explode(" ", $line);
          echo "<tr bgcolor=#FA8258>";
          echo "<td class='medium';> ".trim($pieces2[6], "Agent/")." </td> ";
          if(is_numeric($pieces2[23])) $c1 = $pieces2[23]; else $c1 = "?";
          if(is_numeric($pieces2[19])) $c2 = $pieces2[19]; else $c2 = "?";
          echo "<td class='medium';>$c1 </td> ";
          echo "<td class='medium';>$c2 </td> ";
          echo "</tr>";
         }
       }
      }
//      echo "</table>";
    }



// ECHO AGENTS BUSY

//   echo "<h3><u>Занятые операторы</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
//      echo "<table border='1'; cellpadding=6pt;>";
//      echo "<tr class='heading-medium';><td>Оператор</td><td>Последний вызов, сек</td><td>Вызовов сегодня</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/In use/", $line)) {
          if (!preg_match("/In call/i", $line)) {

          $pieces2 = explode(" ", $line);
          echo "<tr bgcolor=#F4FA58>";
          echo "<td class='medium';> ".trim($pieces2[6], "Agent/")." </td> ";
          if (!preg_match("/paused/i", $line)) {
           if(is_numeric($pieces2[21])) $c1 = $pieces2[21]; else $c1 = "?";
           if(is_numeric($pieces2[17])) $c2 = $pieces2[17]; else $c2 = "?";  
          } else {
             if(is_numeric($pieces2[22])) $c1 = $pieces2[22]; else $c1 = "?";
             if(is_numeric($pieces2[18])) $c2 = $pieces2[18]; else $c2 = "?";
            }
        echo "<td class='medium';>$c1 </td> ";
        echo "<td class='medium';>$c2 </td> ";
          echo "</tr>";
         }
        }
      }
      echo "</table>";
    }



//echo "</td><td>";

// Create two columns
//echo "<br /><table border='0'>";
//echo "<tr><td width=600px;>";

//echo "</td><td>";

// ECHO AGENT ON BREAK


echo "</td><td width=600px valign=top>";

   echo "<h3><u>Операторы на паузе</u></h3>";

    if(!strpos($result['data'], ':'))
      echo $peer['data'];
    else
    {
      $data = array();
      echo "<table border='1'; cellpadding=6pt;>";
      echo "<tr class='heading-medium';><td>Оператор</td><td>Последний вызов, сек</td><td>Вызовов сегодня</td></tr>";
      foreach(explode("\n", $result['data']) as $line)
      {
         if (preg_match("/Not in use/i", $line)) {
           if (preg_match("/paused/i", $line)) {
           $pieces2 = explode(" ", $line);
           echo "<tr bgcolor=#FA58F4>";
           echo "<td class='medium';> ".trim($pieces2[6], "Agent/")." </td> ";
           //echo "<td class='medium';>$pieces2[17] </td> ";
           //echo "<td class='medium';>$pieces2[13] </td> ";
          if(is_numeric($pieces2[23])) $c1 = $pieces2[23]; else $c1 = "?";
          if(is_numeric($pieces2[19])) $c2 = $pieces2[19]; else $c2 = "?";
           echo "<td class='medium';>$c1 </td> ";
           echo "<td class='medium';>$c2 </td> ";
           echo "</tr>";
           }
         }
      }
      echo "</table>";
    }

echo "</td></tr></table>";

    $asm->disconnect();
  }
?>

</body>
</html>
