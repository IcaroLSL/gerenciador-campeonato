<?php
    function clearDataReceived($text){
        $finalText = trim($text);
        $finalText = stripslashes($finalText);     
        $finalText = trim($finalText, "-");  
        $finalText = strip_tags($finalText);
        $finalText = htmlspecialchars($finalText);
        return $finalText;
    }
?>