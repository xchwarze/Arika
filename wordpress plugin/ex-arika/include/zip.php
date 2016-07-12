<?php
# Based on PHPZip - v1.23 by DSR!
class PHPZip {
    var $datasec = array();
    var $ctrl_dir = array();
    var $cut_from_route = 0;
    var $file_count = 0;
    var $old_offset = 0;

    function Zipper($basedir, $filelist){
        $this->cut_from_route = strlen(dirname($basedir . $filelist[0])) + 1;
        foreach ($filelist as $f){   
            $f = $basedir . $f;
            if (is_dir($f))
                $this->AddFolderContent($f);
            else if (is_file($f))
                $this->addFileProc($f);
        }
    }

    function AddFolderContent($dir){
        if (!file_exists($dir))
            return false;
           
        $h = @opendir($dir);
        while (false !== ($f = @readdir($h))) {
            if ($f === '.' || $f === '..')
                continue;

            $f = $dir . $f;
            if (is_dir($f))
                $this->AddFolderContent($f . DIRECTORY_SEPARATOR);
            else if (is_file($f))
                $this->addFileProc($f);
        }
        @closedir($h);
    }

    function addFileProc($file){
        if (!file_exists($file))
            return false;
        
        $this->addFile(file_get_contents($file), substr($file, $this->cut_from_route));
    }

    function unix2DosTime($t = 0){
        $ta = ($t == 0) ? getdate() : getdate($t);
        if ($ta['year'] < 1980) $ta = array('year' => 1980, 'mon' => 1, 'mday' => 1, 'hours' => 0, 'minutes' => 0, 'seconds' => 0);
        return (($ta['year'] - 1980) << 25) | ($ta['mon'] << 21) | ($ta['mday'] << 16) | ($ta['hours'] << 11) | ($ta['minutes'] << 5) | ($ta['seconds'] >> 1);
    }
    
    function hex2bin($s){
        $bin = '';
        $i = 0;
        do {
            $bin .= chr(hexdec($s{$i}.$s{($i + 1)}));
            $i += 2;
        } while ($i < strlen($s));
        return $bin;
    }

    function addFile($data, $name, $time = 0){
        $dtime = dechex($this->unix2DosTime($time));
        $hexdtime = $this->hex2bin($dtime[6] . $dtime[7] . $dtime[4] . $dtime[5] . $dtime[2] . $dtime[3] . $dtime[0] . $dtime[1]);
        $packv0 = pack('v', 0);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);

        // "local file header" segment
        $fr = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00" . $hexdtime;
        $pack_info = pack('V', crc32($data)) . pack('V', strlen($zdata)) . pack('V', strlen($data));
        
        $fr .= $pack_info . pack('v', strlen($name)) . $packv0 . $name;
        $fr .= $zdata; // "file data" segment
        $fr .= $pack_info; // "data descriptor" segment
        $this->datasec[] = $fr;

        // now add to central directory record
        $cdrec = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00" . $hexdtime;
        $cdrec .= $pack_info . pack('v', strlen($name)) . $packv0 . $packv0 . $packv0 . $packv0 . pack('V', 32);
        $cdrec .= pack('V', $this->old_offset) .  $name;

        // save to central directory
        $this->old_offset += strlen($fr);
        $this->file_count += 1;
        $this->ctrl_dir[] = $cdrec;
    }

    function file(){
        $data = implode('', $this->datasec);
        $ctrldir = implode('', $this->ctrl_dir);
        return $data . $ctrldir . "\x50\x4b\x05\x06\x00\x00\x00\x00" . pack('v', $this->file_count) . pack('v', $this->file_count) . pack('V', strlen($ctrldir)) . pack('V', strlen($data)) . "  ";
    }

    function output($file){
        return file_put_contents($file, $this->file());
    }
}

?>