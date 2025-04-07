<?php
/*******************************************************************************
* Software: PDF_ImageAlpha
* Version:  1.4
* Date:     2009-12-28
* Author:   Valentin Schmidt 
*
* Requirements: FPDF 1.6
*
* This script allows to use images (PNGs or JPGs) with alpha-channels. 
* The alpha-channel can be either supplied as separate 8-bit PNG ("mask"), 
* or, for PNGs, also an internal alpha-channel can be used. 
* For the latter the GD 2.x extension is required.
*******************************************************************************/ 

// require('../.inc/fpdf.php');

if (!defined('FPDF_VERSION')) {
    define('FPDF_VERSION', '1.7'); // Define the FPDF_VERSION constant
}

class FPDF_Alpha extends FPDF {
    // Define the FPDF_Alpha class here if it's not already defined

    var $extgstates = array(); // Define the extgstates property

    function SetAlpha($alpha, $bm='Normal') {
        // Set transparency level
        if ($alpha < 0 || $alpha > 1)
            $this->Error('Incorrect alpha value: '.$alpha);
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms) {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs) {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc() {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates() {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_out('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_out(sprintf('/ca %.3F', $parms['ca']));
            $this->_out(sprintf('/CA %.3F', $parms['CA']));
            $this->_out('/BM '.$parms['BM']);
            $this->_out('>>');
            $this->_out('endobj');
        }
    }
}
    
//     function _putresourcedict() {
//         parent::_putresourcedict();
//         if (!empty($this->extgstates)) {
//             $this->_out('/ExtGState <<');
//             foreach($this->extgstates as $k=>$extgstate)
//                 $this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
//             $this->_out('>>');
//         }
//     }

//     function _putresources() {
//         $this->_putextgstates();
//         parent::_putresources();
//     }
// }

class PDF_ImageAlpha extends FPDF_Alpha {
    var $tmpFiles = array(); // Temporary files for mask and image manipulation

    /*******************************************************************************
    *                               Public methods
    *******************************************************************************/
    function ImageAlpha($file, $x, $y, $w = 0, $h = 0, $type = '', $link = '', $isMask = false, $maskImg = 0, $dpi = 72)
    {
        if (!isset($this->images[$file])) {
            if ($type == '') {
                $pos = strrpos($file, '.');
                if (!$pos)
                    $this->Error('Image file has no extension and no type was specified: '.$file);
                $type = substr($file, $pos + 1);
            }
            $type = strtolower($type);

            if ($type == 'jpg' || $type == 'jpeg')
                $info = $this->_parsejpg($file);
            elseif ($type == 'png') {
                $info = $this->_parsepng($file);
                if ($info == 'alpha') return $this->ImagePngWithAlpha($file, $x, $y, $w, $h, $link);
            }
            else {
                $mtd = '_parse'.$type;
                if (!method_exists($this, $mtd))
                    $this->Error('Unsupported image type: '.$type);
                $info = $this->$mtd($file);
            }

            if ($isMask) {
                $info['cs'] = "DeviceGray"; // Force grayscale (instead of indexed)
            }
            $info['i'] = count($this->images) + 1;
            if ($maskImg > 0) $info['masked'] = $maskImg;
            $this->images[$file] = $info;
        }
        else {
            $info = $this->images[$file];
        }

        if ($w == 0 && $h == 0) {
            $w = $info['w'] / $this->k;
            $h = $info['h'] / $this->k;
        }
        if ($w == 0)
            $w = $h * $info['w'] / $info['h'];
        if ($h == 0)
            $h = $w * $info['h'] / $info['w'];

        if ((float)FPDF_VERSION >= 1.7) {
            if ($isMask) $x = ($this->CurOrientation == 'P' ? $this->CurPageSize[0] : $this->CurPageSize[1]) + 10;
        } else {
            if ($isMask) $x = ($this->CurOrientation == 'P' ? $this->CurPageSize[0] : $this->CurPageSize[1]) + 10;
        }

        $this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']));
        if ($link)
            $this->Link($x, $y, $w, $h, $link);

        return $info['i'];
    }

    // Needs GD 2.x extension
    function ImagePngWithAlpha($file, $x, $y, $w = 0, $h = 0, $link = '')
    {
        $tmp_alpha = tempnam('.', 'mska');
        $this->tmpFiles[] = $tmp_alpha;
        $tmp_plain = tempnam('.', 'mskp');
        $this->tmpFiles[] = $tmp_plain;

        list($wpx, $hpx) = getimagesize($file);
        $img = imagecreatefrompng($file);
        $alpha_img = imagecreate($wpx, $hpx);

        for ($c = 0; $c < 256; $c++) ImageColorAllocate($alpha_img, $c, $c, $c);

        $xpx = 0;
        while ($xpx < $wpx) {
            $ypx = 0;
            while ($ypx < $hpx) {
                $color_index = imagecolorat($img, $xpx, $ypx);
                $alpha = 255 - ($color_index >> 24) * 255 / 127; // GD alpha component: 7 bit only, 0..127!
                imagesetpixel($alpha_img, $xpx, $ypx, $alpha);
                ++$ypx;
            }
            ++$xpx;
        }

        imagepng($alpha_img, $tmp_alpha);
        imagedestroy($alpha_img);

        $plain_img = imagecreatetruecolor($wpx, $hpx);
        imagecopy($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx);
        imagepng($plain_img, $tmp_plain);
        imagedestroy($plain_img);

        // First embed mask image (w, h, x, will be ignored)
        $maskImg = $this->Image($tmp_alpha, 0, 0, 0, 0, 'PNG', '', true);

        // Embed image, masked with previously embedded mask
        $this->Image($tmp_plain, $x, $y, $w, $h, 'PNG', $link, false, $maskImg);
    }

    function Close()
    {
        parent::Close();
        // Clean up tmp files
        foreach ($this->tmpFiles as $tmp) @unlink($tmp);
    }

/*******************************************************************************
*                                                                              *
*                               Private methods                                *
*                                                                              *
*******************************************************************************/
function _putimages()
    {
        $filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
        reset($this->images);
        foreach ($this->images as $file => $info) {
            $this->_newobj();
            $this->images[$file]['n'] = $this->n;
            $this->_out('<</Type /XObject');
            $this->_out('/Subtype /Image');
            $this->_out('/Width '.$info['w']);
            $this->_out('/Height '.$info['h']);

            if (isset($info["masked"])) $this->_out('/SMask '.($this->n - 1).' 0 R');

            if ($info['cs'] == 'Indexed')
                $this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal']) / 3 - 1).' '.($this->n + 1).' 0 R]');
            else {
                $this->_out('/ColorSpace /'.$info['cs']);
                if ($info['cs'] == 'DeviceCMYK')
                    $this->_out('/Decode [1 0 1 0 1 0 1 0]');
            }
            $this->_out('/BitsPerComponent '.$info['bpc']);
            if (isset($info['f']))
                $this->_out('/Filter /'.$info['f']);
            if (isset($info['parms']))
                $this->_out($info['parms']);
            if (isset($info['trns']) && is_array($info['trns'])) {
                $trns = '';
                for ($i = 0; $i < count($info['trns']); $i++)
                    $trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
                $this->_out('/Mask ['.$trns.']');
            }
            $this->_out('/Length '.strlen($info['data']).'>>');
            $this->_putstream($info['data']);
            unset($this->images[$file]['data']);
            $this->_out('endobj');
            // Palette
            if ($info['cs'] == 'Indexed') {
                $this->_newobj();
                $pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
                $this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
                $this->_putstream($pal);
                $this->_out('endobj');
            }
        }
    }

    function _parsepng($file)
    {
        $f = fopen($file, 'rb');
        if (!$f)
            $this->Error('Can\'t open image file: '.$file);
        if (fread($f, 8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
            $this->Error('Not a PNG file: '.$file);
        fread($f, 4);
        if (fread($f, 4) != 'IHDR')
            $this->Error('Incorrect PNG file: '.$file);
        $w = $this->_readint($f);
        $h = $this->_readint($f);
        $bpc = ord(fread($f, 1));
        $ct = ord(fread($f, 1));
        $cs = ($ct == 0) ? 'DeviceGray' : 'DeviceRGB';
        if ($ct == 4)
            $cs = 'DeviceCMYK';
        $this->_readint($f);
        $data = fread($f, 4);
        fclose($f);
        return array('w' => $w, 'h' => $h, 'cs' => $cs, 'bpc' => $bpc, 'f' => 'FlateDecode', 'data' => $data);
    }

    function _readint($f)
    {
        $a = unpack('N', fread($f, 4));
        return $a[1];
    }
}
?>