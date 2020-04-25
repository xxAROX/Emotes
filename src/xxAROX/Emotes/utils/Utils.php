<?php
/* Copyright (c) 2020 xxAROX. All rights reserved. */
namespace xxAROX\Emotes\utils;
use pocketmine\entity\Skin;
use xxAROX\Emotes\Emote;
use xxAROX\Emotes\Main;


/**
 * Class Utils
 * @package xxAROX\Emotes\utils
 * @author xxAROX
 * @date 25.04.2020 - 22:21
 * @project Emotes
 */
class Utils{
	private $hat = [
		32  => ["start"=>[0, 0],"end"=>[0,0]],
		64  => ["start"=>[32, 0],"end"=>[64,0]],
		128 => ["start"=>[64, 0],"end"=>[128,0]]
	];

	/**
	 * Function getPlugin
	 * @return Main
	 */
	private function getPlugin(): Main{
		return Main::getInstance();
	}

	/**
	 * Function toImage
	 * @param $data
	 * @param $height
	 * @param $width
	 * @return false|resource
	 */
	public function toImage($data, $height, $width) {
		$pixelarray = str_split(bin2hex($data), 8);
		$image = imagecreatetruecolor($width, $height);
		imagealphablending($image, false);//do not touch
		imagesavealpha($image, true);
		$position = count($pixelarray) - 1;
		while (!empty($pixelarray)){
			$x = $position % $width;
			$y = ($position - $x) / $height;
			$walkable = str_split(array_pop($pixelarray), 2);
			$color = array_map(function ($val){ return hexdec($val); }, $walkable);
			$alpha = array_pop($color); // equivalent to 0 for imagecolorallocatealpha()
			$alpha = ((~((int)$alpha)) & 0xff) >> 1; // back = (($alpha << 1) ^ 0xff) - 1
			array_push($color, $alpha);
			imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, ...$color));
			$position--;
		}
		return $image;
	}

	/**
	 * Function fromImage
	 * @param resource $img
	 * @return string
	 */
	public function fromImage($img): string{
		$bytes = '';
		for ($y = 0; $y < imagesy($img); $y++){
			for ($x = 0; $x < imagesx($img); $x++){
				$rgba = @imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);
			}
		}
		return $bytes;
	}

	/**
	 * Function toSkinData
	 * @param $resource
	 * @return string
	 */
	public function toSkinData($resource): string{
		$img = imagecreatefrompng($resource);
		[$k, $l] = getimagesize($resource);
		$bytes = '';
		for ($y = 0; $y < $l; ++$y) {
			for ($x = 0; $x < $k; ++$x) {
				$argb = imagecolorat($img, $x, $y);
				$bytes .= chr(($argb >> 16) & 0xff).chr(($argb >> 8) & 0xff).chr($argb & 0xff).chr((~($argb >> 24) << 1) & 0xff);
			}
		}
		imagedestroy($img);
		if (strlen($bytes) > 65536) {
			return base64_decode("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pQkW/44SIf+OEiH/xMTE/93d3f/q6ur//////+rq6v/d3d3/3d3d/+rq6v//////6urq/93d3f/ExMT/jhIh/44SIf+lCRb/pQkW/7cJAP/MGwj/jhIh/7cJAP/MGwj/4jQd/+pHKv/MGwj/twkA/44SIf/+jZL//ru+//67vv/+u77/9fey//X3sv/197L/8vSb//L0m//197L/9fey//X3sv+y97j/sve4/7L3uP+b9KP/m/Sj/7L3uP+y97j/sve4/8j6+f/I+vn/yPr5/578+v+e/Pr/yPr5/8j6+f/I+vn//ru+//67vv/+u77//o2S/6UJFv+OEiH/xMTE/93d3f/q6ur//////+rq6v/d3d3/xMTE/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMTExP/d3d3/6urq///////q6ur/3d3d/8TExP+OEiH/pQkW/6UJFv+OEiH/twkA/+pHKv/iNB3/twkA/44SIf+lCRb//o2S//67vv/+u77//ru+//X3sv/197L/9fey//L0m/8AAAD/9fey//X3sv/197L/sve4/7L3uP+y97j/AAAA/5v0o/+y97j/sve4/7L3uP/I+vn/yPr5/8j6+f+e/Pr/nvz6/8j6+f/I+vn/yPr5//67vv/+u77//ru+//6Nkv/d3d3/6urq///////q6ur/3d3d/8TExP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADExMT/3d3d/+rq6v//////6urq/93d3f/ExMT/iAAI/6UJFv/MGwj/zBsI/6UJFv+IAAj/xMTE//6Nkv/8/OD//ru+//67vv/197L/9fey//X3sv/y9Jv/ODg4//X3sv//wk3//8JN///CTf//wk3/sve4/zg4OP+b9KP/sve4/7L3uP+y97j/yPr5/8j6+f/g/OT/nvz6/578+v/I+vn/yPr5/8j6+f/+u77//ru+//z84P/+jZL/3d3d/8TExP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMTExP/d3d3/6urq/8TExP+IAAj/twkA/7cJAP+IAAj/xMTE/+rq6v/+jZL//Pzg//z84P/+u77/9fey//X3sv/197L/8vSb//L0m//197L/9fey//9eTf//Xk3/sve4/7L3uP+b9KP/m/Sj/7L3uP+y97j/sve4/8j6+f/g/OT/4Pzk/578+v+e/Pr/yPr5/8j6+f/I+vn//ru+//z84P/8/OD//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADd3d3/iYmJ///////d3d3/iYmJ/93d3f8AAAAA/o2S//6Nkv/+jZL//o2S//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+e/Pr/nvz6/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6//6Nkv/+jZL//o2S//6Nkv/NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/A1MH/wNTB//NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/zQAA/80AAP/NAAD/zQAA/wNTB//NAAD/A1MH/80AAP/NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/A1MH/wAAAAAAAAAAAAAAAAAAAAD+jZL//o2S//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//6Nkv/+jZL//o2S/578+v+e/Pr/nvz6/578+v/+jZL//o2S//6Nkv/+jZL/nvz6/578+v+e/Pr/nvz6/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//67vv/+u77//o2S//6Nkv/+u77//ru+//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/+u77//ru+//67vv/I+vn/yPr5/8j6+f+e/Pr//o2S//67vv/+u77//ru+/8j6+f/I+vn/yPr5/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/+u77//ru+//6Nkv/+jZL//ru+//67vv/+jZL/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPL0m//197L/9fey//L0m//y9Jv/9fey//X3sv/y9Jv/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADy9Jv/9fey//X3sv/197L/sve4/7L3uP+y97j/m/Sj//L0m//197L/9fey//X3sv+y97j/sve4/7L3uP+b9KP/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADy9Jv/9fey//X3sv/y9Jv/8vSb//X3sv/197L/8vSb/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADy9Jv/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA8vSb//L0m//y9Jv/8vSb/5v0o/+b9KP/m/Sj/5v0o//y9Jv/8vSb//L0m//y9Jv/m/Sj/5v0o/+b9KP/m/Sj/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA8vSb//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//L0m/8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+jZL//o2S//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv//o2S//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S//6Nkv/y9Jv/8vSb//L0m//y9Jv/8vSb//L0m/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+e/Pr/nvz6/578+v+e/Pr/nvz6/578+v/+jZL//o2S//6Nkv/+jZL//o2S//6Nkv/y9Jv/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//z84P/8/OD/8vSb//L0m//8/OD//Pzg//L0m//y9Jv/9fey//67vv/+jZL//o2S//z84P/8/OD//o2S//6Nkv/+u77/9fey//L0m//y9Jv//Pzg//z84P/197L/sve4/7L3uP+y97j/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/yPr5/8j6+f/I+vn//ru+//z84P/8/OD//o2S//6Nkv/8/OD//Pzg//L0m//y9Jv//Pzg//z84P/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/8/OD//Pzg//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/8/OD/9fey//L0m//y9Jv/9fey//z84P/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/+u77//Pzg//6Nkv/+jZL//ru+//X3sv/y9Jv/8vSb//z84P/197L/9fey/7L3uP+y97j/sve4/5v0o/+b9KP/sve4/8j6+f+e/Pr/nvz6/8j6+f/I+vn/yPr5//67vv/+u77//Pzg//6Nkv/+jZL//Pzg//X3sv/y9Jv/8vSb//X3sv/8/OD/8vSb//L0m//197L//ru+//6Nkv/+jZL//Pzg//67vv/+jZL/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/8vSb//L0m//197L//ru+//6Nkv/+jZL//ru+//67vv/+jZL//o2S//67vv/197L/8vSb//L0m//197L/9fey//X3sv+y97j/sve4/7L3uP+b9KP/m/Sj/7L3uP/I+vn/nvz6/578+v/I+vn/yPr5/8j6+f/+u77//ru+//67vv/+jZL//o2S//67vv/197L/8vSb//L0m//197L/9fey//L0m//y9Jv/9fey//67vv/+jZL//o2S//67vv/+u77//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//67vv/197L/8vSb//L0m//197L/9fey//L0m//y9Jv/9fey//67vv/+jZL//o2S//67vv/+u77//o2S//6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/197L/sve4/7L3uP+y97j/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/yPr5/8j6+f/I+vn//ru+//67vv/+u77//o2S//6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/+u77//ru+//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/+u77//ru+//6Nkv/+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/9fey/7L3uP+y97j/sve4/5v0o/+b9KP/sve4/8j6+f+e/Pr/nvz6/8j6+f/I+vn/yPr5//67vv/+u77//ru+//6Nkv/+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/8vSb//L0m//197L//ru+//6Nkv/+jZL//ru+//67vv/+jZL/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/8vSb//L0m//197L//ru+//6Nkv/+jZL//ru+//67vv/+jZL//o2S//67vv/197L/8vSb//L0m//197L/9fey//X3sv+y97j/sve4/7L3uP+b9KP/m/Sj/7L3uP/I+vn/nvz6/578+v/I+vn/yPr5/8j6+f/+u77//ru+//67vv/+jZL//o2S//67vv/197L/8vSb//L0m//197L/9fey//L0m//y9Jv/9fey//67vv/+jZL//o2S//67vv/+u77//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//67vv/197L/8vSb//L0m//197L/9fey//L0m//y9Jv/9fey//67vv/+jZL//o2S//67vv/+u77//o2S//6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/197L/sve4/7L3uP+y97j/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/yPr5/8j6+f/I+vn//ru+//67vv/+u77//o2S//6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/+u77//ru+//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/+u77//ru+//6Nkv/+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/9fey/7L3uP+y97j/sve4/5v0o/+b9KP/sve4/8j6+f+e/Pr/nvz6/8j6+f/I+vn/yPr5//67vv/+u77//ru+//6Nkv/+jZL//ru+//X3sv/y9Jv/8vSb//X3sv/197L/8vSb//L0m//197L//ru+//6Nkv/+jZL//ru+//67vv/+jZL/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+jZL//ru+//z84P/y9Jv/8vSb//z84P/197L/8vSb//L0m//197L//ru+//6Nkv/+jZL//Pzg//67vv/+jZL//o2S//67vv/197L/8vSb//L0m//197L/9fey//X3sv+y97j/sve4/+D85P+b9KP/m/Sj/7L3uP/I+vn/nvz6/578+v/g/OT/yPr5/8j6+f/+u77//ru+//67vv/+jZL//o2S//67vv/8/OD/8vSb//L0m//8/OD/9fey//L0m//y9Jv/9fey//67vv/+jZL//o2S//67vv/8/OD//o2S/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/o2S//z84P/8/OD/8vSb//L0m//8/OD//Pzg//L0m//y9Jv/9fey//67vv/+jZL//o2S//z84P/8/OD//o2S//6Nkv/+u77/9fey//L0m//y9Jv/9fey//X3sv/197L/sve4/+D85P/g/OT/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/4Pzk/+D85P/I+vn//ru+//67vv/+u77//o2S//6Nkv/8/OD//Pzg//L0m//y9Jv//Pzg//z84P/y9Jv/8vSb//X3sv/+u77//o2S//6Nkv/8/OD//Pzg//6Nkv8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP6Nkv/+jZL/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//L0m//+jZL//o2S//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6//6Nkv/+jZL//o2S//6Nkv/+jZL//o2S//L0m//y9Jv/8vSb//L0m//y9Jv/8vSb//L0m//y9Jv//o2S//6Nkv/+jZL//o2S//6Nkv/+jZL/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANTB//NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADUwf/zQAA/wNTB//NAAD/A1MH/80AAP8DUwf/zQAA/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA1MH/80AAP8DUwf/zQAA/wNTB//NAAD/A1MH/80AAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANTB//NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANTB/8DUwf/A1MH/wNTB/8DUwf/zQAA/wNTB//NAAD/A1MH/80AAP8DUwf/zQAA/80AAP/NAAD/zQAA/80AAP/NAAD/A1MH/80AAP8DUwf/zQAA/wNTB//NAAD/A1MH/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANTB//NAAD/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADUwf/zQAA/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA1MH/80AAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANTB/8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACe/Pr/nvz6/578+v+e/Pr/m/Sj/5v0o/+b9KP/m/Sj/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/8j6+f/I+vn/nvz6/5v0o//I+vn/yPr5/5v0o/8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ78+v/I+vn/yPr5/578+v+e/Pr/sve4/7L3uP+e/Pr/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJv0o/+y97j/sve4/5v0o/+b9KP/sve4/7L3uP+b9KP/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACb9KP/sve4/7L3uP+b9KP/m/Sj/7L3uP+y97j/m/Sj/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACb9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAm/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACe/Pr/nvz6/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/nvz6/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6/578+v+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/8j6+f+y97j/m/Sj/5v0o//g/OT/4Pzk/5v0o/+b9KP/4Pzk/+D85P+e/Pr/nvz6/+D85P/g/OT/nvz6/578+v/I+vn/yPr5/5v0o/+b9KP/4Pzk/+D85P+b9KP/m/Sj/+D85P/g/OT/nvz6/578+v/g/OT/4Pzk/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ78+v/I+vn/sve4/5v0o/+b9KP/4Pzk/7L3uP+b9KP/m/Sj/7L3uP/g/OT/nvz6/578+v/g/OT/yPr5/578+v+e/Pr/yPr5/7L3uP+b9KP/m/Sj/+D85P+y97j/m/Sj/5v0o/+y97j/4Pzk/578+v+e/Pr/yPr5/+D85P+e/Pr/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACe/Pr/yPr5/7L3uP+b9KP/m/Sj/7L3uP+y97j/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/yPr5/8j6+f+e/Pr/nvz6/8j6+f+y97j/m/Sj/5v0o/+y97j/sve4/5v0o/+b9KP/sve4/7L3uP+e/Pr/nvz6/8j6+f/I+vn/nvz6/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/8j6+f+y97j/m/Sj/5v0o/+y97j/sve4/5v0o/+b9KP/sve4/8j6+f+e/Pr/nvz6/8j6+f/I+vn/nvz6/578+v/I+vn/sve4/5v0o/+b9KP/sve4/7L3uP+b9KP/m/Sj/7L3uP+y97j/nvz6/578+v/I+vn/yPr5/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ78+v/I+vn/sve4/5v0o/+b9KP/sve4/7L3uP+b9KP/m/Sj/7L3uP/I+vn/nvz6/578+v/I+vn/yPr5/578+v+e/Pr/yPr5/7L3uP+b9KP/m/Sj/7L3uP+y97j/m/Sj/5v0o/+y97j/sve4/578+v+e/Pr/yPr5/8j6+f+e/Pr/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACe/Pr/yPr5/7L3uP+b9KP/m/Sj/7L3uP+y97j/m/Sj/5v0o/+y97j/yPr5/578+v+e/Pr/yPr5/8j6+f+e/Pr/nvz6/8j6+f+y97j/m/Sj/5v0o/+y97j/sve4/5v0o/+b9KP/sve4/7L3uP+e/Pr/nvz6/8j6+f/I+vn/nvz6/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/8j6+f+y97j/m/Sj/5v0o/+y97j/sve4/5v0o/+b9KP/sve4/8j6+f+e/Pr/nvz6/8j6+f/I+vn/nvz6/578+v/I+vn/sve4/5v0o/+b9KP/sve4/7L3uP+b9KP/m/Sj/7L3uP+y97j/nvz6/578+v/I+vn/yPr5/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ78+v/I+vn/sve4/5v0o/+b9KP/sve4/7L3uP+b9KP/m/Sj/7L3uP/I+vn/nvz6/578+v/I+vn/yPr5/578+v+e/Pr/yPr5/7L3uP+b9KP/m/Sj/7L3uP+y97j/m/Sj/5v0o/+y97j/sve4/578+v+e/Pr/yPr5/8j6+f+e/Pr/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACe/Pr/yPr5/7L3uP+b9KP/m/Sj/7L3uP/g/OT/m/Sj/5v0o//g/OT/yPr5/578+v+e/Pr/yPr5/+D85P+e/Pr/nvz6/8j6+f+y97j/m/Sj/5v0o/+y97j/4Pzk/5v0o/+b9KP/4Pzk/7L3uP+e/Pr/nvz6/+D85P/I+vn/nvz6/wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAnvz6/8j6+f+y97j/m/Sj/5v0o//g/OT/4Pzk/5v0o/+b9KP/4Pzk/+D85P+e/Pr/nvz6/+D85P/g/OT/nvz6/578+v/I+vn/sve4/5v0o/+b9KP/4Pzk/+D85P+b9KP/m/Sj/+D85P/g/OT/nvz6/578+v/g/OT/4Pzk/578+v8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAJ78+v+e/Pr/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+e/Pr/nvz6/578+v+e/Pr/nvz6/578+v+e/Pr/nvz6/5v0o/+b9KP/m/Sj/5v0o/+b9KP/m/Sj/5v0o/+b9KP/nvz6/578+v+e/Pr/nvz6/578+v+e/Pr/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=="); //NOTE: xxAROX's Skin.
        }
		return $bytes;
	}

	/**
	 * Function mergeSkin
	 * @param Skin $skin
	 * @param Emote $emote
	 * @return string
	 */
	public function mergeSkin(Skin $skin, Emote $emote): string{
		$baseskin = $skin->getSkinData();
		$resolution = $this->getResolution($baseskin);
		$baseimg = $this->toImage($baseskin, $resolution, $resolution);
		$base = imagecreatetruecolor(imagesx($baseimg), imagesy($baseimg));
		imagesavealpha($base, true);
		imagefill($base, 0, 0, imagecolorallocatealpha($base, 0, 0, 0, 127));

		$img = $this->toImage($emote->getSkinData($resolution), $resolution, $resolution);
		imagecopy($base, $img, $this->hat[$resolution]["start"][0], $this->hat[$resolution]["end"][0], $this->hat[$resolution]["start"][1], $this->hat[$resolution]["end"][1], imagesx($img), imagesy($img));

		return $this->fromImage($base);
	}

	/**
	 * Function getResolution
	 * @param string $skin
	 * @return int
	 */
	public function getResolution(string $skin): int{
		if (strlen($skin) == 8192)
			return 32;
		if (strlen($skin) == 16384)
			return 64;
		if (strlen($skin) == 65536)
			return 128;

		return 64;
	}

	/**
	 * Function getEmoteByName
	 * @param string $name
	 * @return Emote
	 */
	public function getEmoteByName(string $name): ?Emote{
		foreach (Main::$emotes as $emote) {
			if ($emote->getName() === $name) {
				return $emote;
			}
		}
		return NULL;
	}
}
