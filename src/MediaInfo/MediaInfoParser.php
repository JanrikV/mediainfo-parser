<?php

class MediaInfo {
    private $regex_section = "/^(?:(?:General|Video|Audio|Text|Menu)(?:\s\#\d+?)*)$/i";

    public function parse($string)
    {
        $string = trim($string);
        $lines = preg_split("/\R/", $string);

        $output = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match($this->regex_section, $line)) {
                $section = $line;
                $output[$section] = [];
            }
            if (isset($section)) {
                $output[$section][] = $line;
            } else {
                $output['General'][] = $line;
            }
        }


        if (count($output)) {
            $output = $this->parseSections($output);
        }


        return $this->formatOutput($output);
    }

    private function parseSections(array $sections)
    {
        $output = [];
        foreach ($sections as $key => $section) {
            $key_section = explode(' ', $key)[0];
            if (!empty($section)) {
                if ($key_section == 'General') {
                    $output[$key_section] = $this->parseProperty($section, $key_section);
                } else {
                    $output[$key_section][] = $this->parseProperty($section, $key_section);
                }
            }
        }

        return $output;
    }

    private function parseProperty($sections, $section)
    {
        $output = [];
        foreach ($sections as $info) {
            $property = null;
            $value = null;
            $info = explode(":", $info, 2);
            if (count($info) >= 2) {
                $property = trim($info[0]);
                $value = trim($info[1]);
            }
            if ($property && $value) {
                switch ($section) {

                    case 'General':
                    case '':
                        switch ($property) {
                            case "Complete name":
                            case "completename":
                                $output['file_name'] = $this->stripPath($value);
                                break;
                            case "Format":
                                $output['format'] = $value;
                                break;
                            case "Duration":
                                $output['duration'] = $value;
                                break;
                            case "File size":
                            case "filesize":
                                $output['file_size'] = $this->parseFileSize($value);
                                break;
                            case "Overall bit rate":
                            case "overallbitrate":
                                $output['bit_rate'] = $this->parseBitRate($value);
                                break;
                        }
                        break;

                    case 'Video':
                        switch ($property) {
                            case "Format":
                                $output['format'] = $value;
                                break;
                            case "Format version":
                            case "format_version":
                                $output['format_version'] = $value;
                                break;
                            case "Codec ID":
                            case "codecid":
                                $output['codec'] = $value;
                                break;
                            case "Width":
                                $output['width'] = $this->parseWidthHeight($value);
                                $output['anamorphic_width'] = $this->parseWidthHeight($value, true);
                                break;
                            case "Height":
                                $output['height'] = $this->parseWidthHeight($value);
                                $output['anamorphic_height'] = $this->parseWidthHeight($value, true);
                                break;
                            case "Stream size":
                            case "stream_size":
                                $output['stream_size'] = $this->parseFileSize($value);
                                break;
                            case "Writing library":
                            case "encoded_library":
                                $output['writing_library'] = $value;
                                break;
                            case "Frame rate mode":
                            case "framerate_mode":
                                $output['framerate_mode'] = $value;
                                break;
                            case "Frame rate":
                            case "framerate":
                                // If variable this becomes Original frame rate
                                $output['frame_rate'] = $value;
                                break;
                            case "Display aspect ratio":
                            case "displayaspectratio":
                                $output['aspect_ratio'] = str_replace("/", ":",
                                    $value); // Mediainfo sometimes uses / instead of :
                                break;
                            case "Bit rate":
                            case "bitrate":
                                $output['bit_rate'] = $this->parseBitRate($value);
                                break;
                            case "Bit rate mode":
                            case "bitrate_mode":
                                $output['bit_rate_mode'] = $value;
                                break;
                            case "Nominal bit rate":
                            case "bitrate_nominal":
                                $output['bit_rate_nominal'] = $this->parseBitRate($value);
                                break;
                            case "Bits/(Pixel*Frame)":
                            case "bits-(pixel*frame)":
                                $output['bit_pixel_frame'] = $value;
                                break;
                            case "Bit depth":
                            case "bitdepth":
                                $output['bit_depth'] = $value;
                                break;
                            case "Encoding settings":
                                $output['encoding_settings'] = $value;
                                break;
                            case "Language":
                                $output['language'] = $value;
                                break;
                        }
                        break;

                    case 'Audio':
                        switch ($property) {
                            case "ID":
                                $output['id'] = $value;
                            break;
                            case "Codec ID":
                            case "codecid":
                                $output['codec'] = $value;
                                break;
                            case "Format":
                                $output['format'] = $value;
                                break;
                            case "Bit rate":
                            case "bitrate":
                                $output['bit_rate'] = $this->parseBitRate($value);
                                break;
                            case "Channel(s)":
                                $output['channels'] = $this->parseAudioChannels($value);
                                break;
                            case "Title":
                                $output['title'] = $value;
                                break;
                            case "Language":
                                $output['language'] = $value;
                                break;
                            case "Format profile":
                            case "format_profile":
                                $output['format_profile'] = $value;
                                break;
                            case "Stream size":
                            case "stream_size":
                                $output['stream_size'] = $this->parseFileSize($value);
                                break;
                        }
                        break;

                    case 'Text':
                        switch ($property) {
                            case "ID":
                                $output['id'] = $value;
                            break;
                            case "Codec ID":
                            case "codecid":
                                $output['codec'] = $value;
                                break;
                            case "Format":
                                $output['format'] = $value;
                                break;
                            case "Title":
                                $output['title'] = $value;
                                break;
                            case "Language":
                                $output['language'] = $value;
                                break;
                            case "Default":
                                $output['default'] = $value;
                                break;
                            case "Forced":
                                $output['forced'] = $value;
                                break;
                        }
                        break;

                }
            }
        }

        return $output;
    }

    private function stripPath($string)
    {
        $string = str_replace("\\", "/", $string);
        $path_parts = pathinfo($string);
        
        return $path_parts['filename'];
    }

    private function parseFileSize($string)
    {
        $number = (float)str_replace(' ', '', $string);
        preg_match("/[KMGTPEZ]/i", $string, $size);
        if (!empty($size[0])) {
            $number = $this->computerSize($number, $size[0] . 'b');
        }

        return $number;
    }

    private function parseBitRate($string)
    {
        $string = str_replace(' ', '', $string);
        $string = str_replace('kbps', ' kbps', $string);

        return $string;
    }

    private function parseWidthHeight($string, $anamorphic = false)
    {
        $pixels = str_replace(['pixels', ' '], null, $string);
        if (strstr($pixels, '>>')) {
            $parsed = explode('>>', $pixels);
            if ($anamorphic) {
                if (isset($parsed[0])) {
                    return $parsed[0];
                }
            }
            if (isset($parsed[1])) {
                return $parsed[1];
            }
        }

        if ($anamorphic) {
            return null;
        }

        return $pixels;
    }

    private function parseAudioChannels($string)
    {
        $replace = [
            ' '        => '',
            'channels' => 'ch',
            'channel'  => 'ch',
            '1ch'      => '1.0ch',
            '7ch'      => '6.1ch',
            '6ch'      => '5.1ch',
            '2ch'      => '2.0ch',
        ];

        return str_ireplace(array_keys($replace), $replace, $string);
    }

    private function formatOutput($data)
    {
        $output = [];
        $output['general'] = !empty($data['General']) ? $data['General'] : null;
        $output['video'] = !empty($data['Video']) ? $data['Video'] : null;
        $output['audio'] = !empty($data['Audio']) ? $data['Audio'] : null;
        $output['text'] = !empty($data['Text']) ? $data['Text'] : null;

        return $output;
    }

    private function parseAudioFormat($string)
    {

    }

    private function computerSize($number, $size)
    {
        $bytes = (float)$number;
        $size = strtolower($size);

        $factors = ['b' => 0, 'kb' => 1, 'mb' => 2, 'gb' => 3, 'tb' => 4, 'pb' => 5, 'eb' => 6, 'zb' => 7, 'yb' => 8];

        if (isset($factors[$size])) {
            return (float)number_format($bytes * pow(1024, $factors[$size]), 2, '.', '');
        }

        return $bytes;
    }
}
