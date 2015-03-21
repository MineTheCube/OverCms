<?php

Class Captcha {
    
    public function __construct() {
    }

    public function check($text) {

        if (session_id() == '')
            session_start();

        $solution = $_SESSION['captcha'];
        if (empty($text) or empty($solution))
            return false;
        if ($solution == $text)
            return true;
        return false;
    }
    
    public function get() {

        return HTTP_ROOT.PACKAGE.'captcha/picture.php?/captcha.png';

    }

    public function display() {

        if (session_id() == '')
            session_start();

        $image = @imagecreatetruecolor(114, 42);
        if ($image === false) {
            return false;
        }
        
        $background_color = imagecolorallocate($image, 181, 231, 181);
        $text_color = imagecolorallocate($image, 33, 140, 132);
        $line_color = imagecolorallocate($image, 170, 200, 170);
        $round_color = imagecolorallocate($image, 189, 247, 198);
        $border = imagecolorallocate($image, 100, 100, 100);

        imagefilledrectangle($image, 0, 0, 114, 42, $background_color);
        imagefilledellipse($image, 0+rand(0,72) , 0+rand(0,21) , 50+rand(0,50), 30+rand(0,20), $round_color);
        imagefilledellipse($image, 72+rand(0,72) , 21+rand(0,21) , 50+rand(0,50), 30+rand(0,20), $round_color);

        for ($i = 0; $i < 5; $i++) {
            $x1 = 0 + rand(-10, 20);
            $y1 = 0 + rand(-20, 62);
            $x2 = 114 + rand(-20, 10);
            $y2 = 42 + rand(-62, 20);
            imageline($image, $x1, $y1, $x2, $y2, $line_color);
        }

        $letters = 'ABCDEFGHIJKLMNPQRSUVWXYZabcdefhijkmnpqrsuvwxyz2345678';
        $len = strlen($letters);

        $word = "";
        $font = str_replace('captcha.class.php', 'font.ttf', __FILE__);
        for ($i = 0; $i < 5; $i++) {
            $letter = $letters[rand(0, $len - 1)];
            if (ctype_alpha($letter) and ctype_upper($letter))
                imagettftext ($image, 18, rand(-10, 10), 7+($i*20)+rand(0,2), 23+rand(0, 10), $text_color, $font, $letter);
            else if (ctype_alpha($letter) and ctype_lower($letter))
                imagettftext ($image, 16, rand(-10, 10), 7+($i*20)+rand(0,2), 23+rand(0, 10), $text_color, $font, $letter);
            else
                imagettftext ($image, 17, rand(-10, 10), 7+($i*20)+rand(0,2), 23+rand(0, 10), $text_color, $font, $letter);
            $word .= $letter;
        }

        $_SESSION['captcha'] = $word;
        
        imageline($image, 0, 0, 114, 0, $border);
        imageline($image, 0, 0, 0, 42, $border);
        imageline($image, 113, 41, 113, 0, $border);
        imageline($image, 0, 41, 113, 41, $border);

        header('Content-Type: image/png');
        imagepng($image);

    }
    
}