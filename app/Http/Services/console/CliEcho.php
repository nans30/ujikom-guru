<?php

namespace App\Http\Services\console;

class CliEcho
{
    private array $foreground_colors = [];

    private array $background_colors = [];

    private static array $foregroundColors = [
        'black'        => '0;30',
        'dark_gray'    => '1;30',
        'blue'         => '0;34',
        'light_blue'   => '1;34',
        'green'        => '0;32',
        'light_green'  => '1;32',
        'cyan'         => '0;36',
        'light_cyan'   => '1;36',
        'red'          => '0;31',
        'light_red'    => '1;31',
        'purple'       => '0;35',
        'light_purple' => '1;35',
        'brown'        => '0;33',
        'yellow'       => '1;33',
        'light_gray'   => '0;37',
        'white'        => '1;37',
    ];

    private static array $backgroundColors = [
        'black'      => '40',
        'red'        => '41',
        'green'      => '42',
        'yellow'     => '43',
        'blue'       => '44',
        'magenta'    => '45',
        'cyan'       => '46',
        'light_gray' => '47',
    ];

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';
        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    // Returns colored string
    public function getColoredString($string, $foreground_color = null, $background_color = null, $new_line = false): string
    {
        $colored_string = '';
        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[".$this->foreground_colors[$foreground_color].'m';
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[".$this->background_colors[$background_color].'m';
        }
        // Add string and end coloring
        $colored_string .= $string."\033[0m";
        return $new_line ? $colored_string.PHP_EOL : $colored_string;
    }

    // Returns all foreground color names
    public function getForegroundColors(): array
    {
        return array_keys($this->foreground_colors);
    }

    // Returns all background color names
    public function getBackgroundColors(): array
    {
        return array_keys($this->background_colors);
    }

    /**
     * Mendapatkan teks berwarna.
     *
     * @param string $string          Teks yang akan diwarnai
     * @param string|null $foregroundColor Warna teks (opsional)
     * @param string|null $backgroundColor Warna latar belakang (opsional)
     *
     * @return string
     */
    public static function initColoredString(
        string $string,
        string $foregroundColor = null,
        string $backgroundColor = null
    ): string
    {
        $coloredString = '';
        if (isset(static::$foregroundColors[$foregroundColor])) {
            $coloredString .= "\033[".static::$foregroundColors[$foregroundColor].'m';
        }
        if (isset(static::$backgroundColors[$backgroundColor])) {
            $coloredString .= "\033[".static::$backgroundColors[$backgroundColor].'m';
        }
        $coloredString .= $string."\033[0m";
        return $coloredString;
    }

    /**
     * Menampilkan pesan informasi.
     *
     * @param $msg
     */
    public static function notice($msg)
    {
        fwrite(STDOUT, self::initColoredString($msg, 'light_gray').PHP_EOL);
    }

    /**
     * Menampilkan pesan kesalahan.
     *
     * @param $msg
     */
    public static function error($msg)
    {
        fwrite(STDERR, self::initColoredString($msg, 'white','red').PHP_EOL);
    }

    /**
     * Menampilkan pesan peringatan.
     *
     * @param $msg
     */
    public static function warn($msg)
    {
        fwrite(STDOUT, self::initColoredString($msg, 'red','yellow').PHP_EOL);
    }

    /**
     * Menampilkan pesan sukses.
     *
     * @param $msg
     */
    public static function success($msg)
    {
        fwrite(STDOUT, self::initColoredString($msg, 'light_cyan').PHP_EOL);
    }

}
