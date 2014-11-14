<?php

Class Captcha {
    
    public function __construct() {
    }

    public function check($text) {
        $session = new Session();
        $solution = $session->get('capcha');
        if (empty($text) or empty($solution))
            return false;
        if ($solution == $text)
            return true;
        return false;
    }
    
    public function generate() {

        $image = @imagecreatetruecolor(114, 42);
        if ($image === false) {
            throw new Exception('MISSING_IMAGECD');
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
        $letter = $letters[rand(0, $len - 1)];

        $word = "";
        $font = str_replace('captcha.class.php', 'font.ttf', __FILE__);
        for ($i = 0; $i < 5; $i++) {
            $letter = $letters[rand(0, $len - 1)];
            imagettftext ($image, 16+rand(0,2), rand(-10, 10), 8+($i*22), 23+rand(0, 10), $text_color, $font, $letter);
            $word .= $letter;
        }
        
        $session = new Session();
        $session->set('capcha', $word);
        
        imageline($image, 0, 0, 114, 0, $border);
        imageline($image, 0, 0, 0, 42, $border);
        imageline($image, 113, 41, 113, 0, $border);
        imageline($image, 0, 41, 113, 41, $border);
        
        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        $data = base64_encode($data);
        
        return "data:image/png;base64,".$data;
    }
    
}