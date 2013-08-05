<?php
/**
 *author: mr.v
 *website: www.okvee.net
 *
 *การใช้งานจะแยกเป็น 3 ส่วนหลักๆ คือ modify(crop/no_resize/resize/resize_crop/resize_no_ratio/zoom_crop) addon(watermark/watermark_easy) และ display(save/show)
 */

class vimage {
	public $image_file1;
	public $image_zoom = false;// value true or false
	public $new_height;
	public $new_width;
	
	private $image_read;
	private $image_height;
	private $image_type;
	private $image_width;
	private $new_image;
	
	/**
	 *$imagefile1 กำหนดเป็น full path ของรูปมา เช่น /home/dir/image.jpg ถ้าหากเป็น dir/image.jpg จะเกิดปัญหาภายหลัง
	*/
	public function __construct($image_file1) {
		if (file_exists($image_file1)) {
			$this->image_file1 = $image_file1;
			// find image size
			$size = getimagesize($image_file1);
			if ($size != false) {
				$width = $size[0];
				$height = $size[1];
				$imgtype_xxx = $size[2];// convert from number(xxx) to ext by image_type_to_extension($imgtype_xxx)
				// value of xxx 1=gif/2=jpeg/3=png/4=swf/5=psd/6=bmp/7=tiff/8=tiff?/9=jpc/10=jp2/11=jpx/12=jb2/13=swf/14=iff/15=bmp/16=xbm/
				//$mime = $size['mime'];// eg. image/gif, image/jpeg
				$this->image_width = $width;
				$this->image_height = $height;
				$this->image_type = $imgtype_xxx;
				unset($size);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}// __construct
	
	public function destroy() {
		@imagedestroy($this->image_read);
		@imagedestroy($this->new_image);
	}// destroy
	
	/**
	 *image_size()
	 *return image size of image source(image_file1)
	 *result in array['width'] and ['height']
	*/
	public function image_size() {
		$output['width'] = $this->image_width;
		$output['height'] = $this->image_height;
		return $output;
	}// image_size
	
	/*----------------------------------------------------------------ส่วน modify----------------------------------------------------------------*/
	
	/**
	 *crop($width, $height, $startx="0", $starty="0")
	 *crop image without resize. just crop.
	 *$startx = start x position
	 *$starty = start y position
	*/
	public function crop($width, $height, $startx="0", $starty="0") {
		if ($this->image_zoom == false) {
			$width = ($this->image_width < $width ? $this->image_width : $width);
			$height = ($this->image_height < $height ? $this->image_height : $height);
		}
		$new_image = imagecreatetruecolor($width, $height);
		if ("1" == $this->image_type) {
			// gif image
			$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);// set color transparent white
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			$image_read = imagecreatefromgif($this->image_file1);
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} elseif ($this->image_type == "2") {
			// jpeg image
			$image_read = imagecreatefromjpeg($this->image_file1);
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} elseif ($this->image_type == "3") {
			// png image
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$image_read = imagecreatefrompng($this->image_file1);
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} else {
			imagedestroy($new_image);
			$this->destroy();
			return false;
		}
		// end crop process
		$this->image_read = $image_read;
		$this->new_height = $height;
		$this->new_image = $new_image;
		$this->new_width = $width;
	}// crop
	
	/**
	 *no_resize()
	 *just create image for addon like watermark
	*/
	public function no_resize() {
		return $this->resize_no_ratio($this->image_width, $this->image_height);
	}// no_resize
	
	/**
	 *resize($width, $height)
	 *resize by aspect ratio
	 *eg w=600 h=400 means width not over 600 and height not over 400 if w>h then h is main value ; if w<h then w is main value
	*/
	public function resize($width, $height) {
		return $this->resize_ratio($width, $height);
	}// resize
	
	/**
	 *resize_crop($width, $height, $startx="", $starty="")
	 *resize to fit crop size in width or height.
	 *left startx and starty for crop at center of image.
	*/
	public function resize_crop($width, $height, $startx="", $starty="") {
		if ($this->image_zoom == false) {
			// reduce size to fit image size (zoom = false)
			if ($this->image_width < $width) {
				$newwidth = $this->image_width;
				$width = $this->image_width;
			} else {
				$newwidth = $width;
			}
			if ($this->image_height < $height) {
				$newheight = $this->image_height;
				$height = $this->image_height;
			} else {
				$newheight = $height;
			}
			// calculate again about width & height aspect ratio
			if ($this->image_width > $this->image_height && $width >= $height) {// ภาพแนวนอน ขนาดครอบกว้างมากกว่าสูง
				$newheight = round(($this->image_height/$this->image_width)*$newwidth);
				if ($newheight < $height) { $height = $newheight; }
				$newwidth = $newwidth;
				$startx = ($startx == null ? "0" : $startx);
				$starty = ($starty == null ?(($newheight/2) >= ($height/2) ? ($newheight/2)-($height/2) : "0") : $starty);
			} elseif ($this->image_width > $this->image_height && $width < $height) {// ภาพแนวนอน ขนาดครอบกว้างน้อยกว่าสูง
				$newheight = $newheight;
				$newwidth = round(($this->image_width/$this->image_height)*$newheight);
				if ($newwidth < $width) { $width = $newwidth; }
				$startx = ($startx == null ?(($newwidth/2) >= ($width/2) ? ($newwidth/2)-($width/2) : "0") : $startx);
				$starty = ($starty == null ? "0" : $starty);
			} elseif ($this->image_height > $this->image_width && $height >= $width) {// ภาพแนวตั้ง ขนาดครอบสูงมากกว่ากว้าง
				$newheight = $newheight;
				$newwidth = round(($this->image_width/$this->image_height)*$newheight);
				if ($newwidth < $width) { $width = $newwidth; }
				$startx = ($startx == null ?(($newwidth/2) >= ($width/2) ? ($newwidth/2)-($width/2) : "0") : $startx);
				$starty = ($starty == null ? "0" : $starty);
			} elseif ($this->image_height > $this->image_width && $height < $width) {// ภาพแนวตั้ง ขนาดครอบสูงน้อยกว่ากว้าง
				$newheight =round(($this->image_height/$this->image_width)*$newwidth);
				if ($newheight < $height) { $height = $newheight; }
				$newwidth = $newwidth;
				$startx = ($startx == null ? "0" : $startx);
				$starty = ($starty == null ?(($newheight/2) >= ($height/2) ? ($newheight/2)-($height/2) : "0") : $starty);
			} else {
				$newheight = $newheight;
				$newwidth = $newwidth;
			}
		} else {// enable image zoom (it's not zoom crop)
			$newwidth = $width;
			$newheight = $height;
			// calculate again about width & height aspect ratio
			if ($this->image_width > $this->image_height && $width >= $height) {// ภาพแนวนอน ขนาดครอบกว้างมากกว่าสูง
				$newheight = round(($this->image_height/$this->image_width)*$newwidth);
				if ($newheight < $height) { $height = $newheight; }
				$newwidth = $newwidth;
				$startx = ($startx == null ? "0" : $startx);
				$starty = ($starty == null ?(($newheight/2) >= ($height/2) ? ($newheight/2)-($height/2) : "0") : $starty);
			} elseif ($this->image_width > $this->image_height && $width < $height) {// ภาพแนวนอน ขนาดครอบกว้างน้อยกว่าสูง
				$newheight = $newheight;
				$newwidth = round(($this->image_width/$this->image_height)*$newheight);
				if ($newwidth < $width) { $width = $newwidth; }
				$startx = ($startx == null ?(($newwidth/2) >= ($width/2) ? ($newwidth/2)-($width/2) : "0") : $startx);
				$starty = ($starty == null ? "0" : $starty);
			} elseif ($this->image_height > $this->image_width && $height >= $width) {// ภาพแนวตั้ง ขนาดครอบสูงมากกว่ากว้าง
				$newheight = $newheight;
				$newwidth = round(($this->image_width/$this->image_height)*$newheight);
				if ($newwidth < $width) { $width = $newwidth; }
				$startx = ($startx == null ?(($newwidth/2) >= ($width/2) ? ($newwidth/2)-($width/2) : "0") : $startx);
				$starty = ($starty == null ? "0" : $starty);
			} elseif ($this->image_height > $this->image_width && $height < $width) {// ภาพแนวตั้ง ขนาดครอบสูงน้อยกว่ากว้าง
				$newheight =round(($this->image_height/$this->image_width)*$newwidth);
				if ($newheight < $height) { $height = $newheight; }
				$newwidth = $newwidth;
				$startx = ($startx == null ? "0" : $startx);
				$starty = ($starty == null ?(($newheight/2) >= ($height/2) ? ($newheight/2)-($height/2) : "0") : $starty);
			} else {
				$newheight = $newheight;
				$newwidth = $newwidth;
			}
		}
		$resizewidth = $newwidth;
		$resizeheight = $newheight;
		$resize = $this->resize_no_ratio($resizewidth, $resizeheight);
		$startx = ($startx == null ? "0" : $startx);
		$starty = ($starty == null ? "0" : $starty);
		//
		$image_read = $this->new_image;
		$new_image = imagecreatetruecolor($width, $height);
		if ("1" == $this->image_type) {
			// gif image
			$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);// set color transparent white
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} elseif ($this->image_type == "2") {
			// jpeg image
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} elseif ($this->image_type == "3") {
			// png image
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $image_read, 0, 0, $startx, $starty, $width, $height);
		} else {
			imagedestroy($image_read);
			imagedestroy($new_image);
			$this->destroy();
			return false;
		}
		// end crop process
		$this->image_read = $image_read;
		$this->new_height = $height;
		$this->new_image = $new_image;
		$this->new_width = $width;
		imagedestroy($image_read);
	}// resize_crop
	
	/**
	 *resize_no_ratio($width, $height)
	 *resize without aspect ratio
	 *$width and $height is just number (int)
	*/
	public function resize_no_ratio($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		// start resize process
		if ("1" == $this->image_type) {
			// gif image
			$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);// set color transparent white
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			$image_read = imagecreatefromgif($this->image_file1);
			imagecopyresampled($new_image, $image_read, 0, 0, 0, 0, $width, $height, $this->image_width, $this->image_height);// ย่อรูปตามขนาด
			imagesavealpha($image_read, true);
			// cls
			unset($transwhite);
		} elseif ($this->image_type == "2") {
			// jpeg image
			$image_read = imagecreatefromjpeg($this->image_file1);
			imagecopyresampled($new_image, $image_read, 0, 0, 0, 0, $width, $height, $this->image_width, $this->image_height);// ย่อรูปตามขนาด
		} elseif ($this->image_type == "3") {
			// png image
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			$image_read = imagecreatefrompng($this->image_file1);
			imagecopyresampled($new_image, $image_read, 0, 0, 0, 0, $width, $height, $this->image_width, $this->image_height);// ย่อรูปตามขนาด
		} else {
			imagedestroy($new_image);
			$this->destroy();
			return false;
		}
		// end resize process
		$this->image_read = $image_read;
		$this->new_height = $height;
		$this->new_image = $new_image;
		$this->new_width = $width;
		// clear
		imagedestroy($image_read);
	}// resize_no_ratio
	
	/**
	 *resize_ratio($width, $height)
	 *resize by aspect ratio
	 *eg w=600 h=400 means width not over 600 and height not over 400 if w>h then h is main value ; if w<h then w is main value
	*/
	public function resize_ratio($width, $height) {
		$find_h_from_w = round(($this->image_height/$this->image_width)*$width);
		$find_w_from_h = round(($this->image_width/$this->image_height)*$height);
		// determine which value is larger than limit.
		if ($find_h_from_w > $height) {
			$width = $find_w_from_h;
			$height = $height;
		} elseif($find_w_from_h > $width) {
			$width = $width;
			$height = $find_h_from_w;
		}
		if ($this->image_zoom == false) {
			// check if newsize is smaller than original size ok.
			// i means if no zoom or zoom = false
			if (($width*$height) > ($this->image_width*$this->image_height)) {
				$width = $this->image_width;
				$height = $this->image_height;
			}
		}
		unset($find_h_from_w, $find_w_from_h);
		return $this->resize_no_ratio($width, $height);
	}// resize_ratio
	
	/**
	 *zoom_crop($width, $height, $startx="", $starty="")
	 *zoom image out or in(image_zoom = true) to fit crop size.
	 *$width = crop width
	 *$height = crop height
	 *$startx = start at x position
	 *$starty = start at y position startx and y leave blank for center position
	*/
	public function zoom_crop($width, $height, $startx = "", $starty = "") {
		if ($this->image_zoom == false) {// ไม่สามารถซูมขยายรูปได้
			if ($width > $height) {// ครอปแนวนอน--------------------------------------------------------------------------------
				if ($this->image_height > $height) {// ภาพสูงกว่าครอป.
					$newwidth = ($this->image_width/$this->image_height)*$height;
					$newheight = $height;
					if ($newwidth > $width) {
						$newwidth = $width;
						$newheight = ($this->image_height/$this->image_width)*$newwidth;
						$startx = ($startx == null ? "0" : $startx);
						$starty = (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0");
					} else {
						$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
						$starty = ($starty == null ? "0" : $starty);
					}
				} else {
					$newwidth = $this->image_width;
					$newheight = $this->image_height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				}
			} elseif ($height > $width) {// ครอปแนวตั้ง--------------------------------------------------------------------------------
				if ($this->image_width > $width) {// ภาพกว้างกว่าครอป
					$newwidth = $width;
					$newheight = ($this->image_height/$this->image_width)*$newwidth;
					if ($newheight > $height) {
						$newwidth = ($this->image_width/$this->image_height)*$height;
						$newheight = $height;
						$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
						$starty = ($starty == null ? "0" : $starty);
					} else {
						$startx = ($startx == null ? "0" : $startx);
						$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
					}
				} else {
					$newwidth = $this->image_width;
					$newheight = $this->image_height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				}
			} else {// ครอปขนาดจตุรัส--------------------------------------------------------------------------------
				if (($this->image_width >= $this->image_height) && ($this->image_width > $width)) {// รูปแนวนอนและภาพใหญ่กว่าครอป
					$newwidth = $width;
					$newheight = ($this->image_height/$this->image_width)*$newwidth;
					$startx = ($startx == null ? "0" : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				} elseif (($this->image_width < $this->image_height) && ($this->image_height > $height)) {// รูปแนวตั้ง และภาพสูงกว่าครอป.
					$newwidth = ($this->image_width/$this->image_height)*$height;
					$newheight = $height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				} else {
					$newwidth = $this->image_width;
					$newheight = $this->image_height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				}
			}
		} else {// ซูมขยายรูปได้
			if ($width > $height) {// ครอปแนวนอน--------------------------------------------------------------------------------
				$newwidth = ($this->image_width/$this->image_height)*$height;
				$newheight = $height;
				if ($newwidth > $width) {
					$newwidth = $width;
					$newheight = ($this->image_height/$this->image_width)*$newwidth;
					$startx = ($startx == null ? "0" : $startx);
					$starty = (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0");
				} else {
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				}
			} elseif ($height > $width) {// ครอปแนวตั้ง--------------------------------------------------------------------------------
				$newwidth = $width;
				$newheight = ($this->image_height/$this->image_width)*$newwidth;
				if ($newheight > $height) {
					$newwidth = ($this->image_width/$this->image_height)*$height;
					$newheight = $height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				} else {
					$startx = ($startx == null ? "0" : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				}
			} else {// ครอปขนาดจตุรัส--------------------------------------------------------------------------------
				if (($this->image_width >= $this->image_height) && ($this->image_width > $width)) {// รูปแนวนอนและภาพใหญ่กว่าครอป
					$newwidth = $width;
					$newheight = ($this->image_height/$this->image_width)*$newwidth;
					$startx = ($startx == null ? "0" : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				} elseif (($this->image_width < $this->image_height) && ($this->image_height > $height)) {// รูปแนวตั้ง และภาพสูงกว่าครอป.
					$newwidth = ($this->image_width/$this->image_height)*$height;
					$newheight = $height;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				} else {
					$newwidth = $width;
					$newheight = ($this->image_height/$this->image_width)*$newwidth;
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $startx);
				}
				
			}
		}
		// start to resize or zoom
		$resizewidth = $newwidth;
		$resizeheight = $newheight;
		$resize = $this->resize_no_ratio($resizewidth, $resizeheight);
		$startx = ($startx == null ? "0" : $startx);
		$starty = ($starty == null ? "0" : $starty);
		//start to crop
		$image_read = $this->new_image;
		$new_image = imagecreatetruecolor($width, $height);
		$black = imagecolorallocate($new_image, 0, 0, 0);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);// set color transparent white
		if ("1" == $this->image_type) {
			// gif image
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} elseif ($this->image_type == "2") {
			// jpeg image
			imagefill($new_image, 0, 0, $white);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} elseif ($this->image_type == "3") {
			// png image
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $black);
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} else {
			imagedestroy($image_read);
			imagedestroy($new_image);
			$this->destroy();
			return false;
		}
		// end crop process
		$this->image_read = $image_read;
		$this->new_height = $height;
		$this->new_image = $new_image;
		$this->new_width = $width;
		imagedestroy($image_read);
	}// zoom_crop
	
	
	/**
	 * zoom_crop_fit
	 * เหมือนกับ zoom_crop แต่จะย่อภาพแบบเต็มๆ ไม่มีส่วนล้นขาวออกมาให้เห็น(กรณีภาพใหญ่กว่าที่จะย่อ)
	 */
	function zoom_crop_fit($width, $height, $startx = '', $starty = '') {
		// no zoom
		if ($width > $height) {// ครอปแนวนอน
			if ($this->image_height > $height) {// ภาพสูงกว่าครอป.
				$newwidth = $width;
				$newheight = ($this->image_height/$this->image_width)*$newwidth;
				if ($newwidth > $width) {
					$startx = ($startx == null ? "0" : $startx);
					$starty = (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0");
				} else {
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				}
			} else {
				$newwidth = $this->image_width;
				$newheight = $this->image_height;
				$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
				$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $starty);
			}
		} elseif ($height > $width) {// ครอปแนวตั้ง
			if ($this->image_width > $width) {// ภาพกว้างกว่าครอป
				$newwidth = ($this->image_width/$this->image_height)*$height;
				$newheight = $height;
				if ($newheight > $height) {
					$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
					$starty = ($starty == null ? "0" : $starty);
				} else {
					$startx = ($startx == null ? "0" : $startx);
					$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $starty);
				}
			} else {
				$newwidth = $this->image_width;
				$newheight = $this->image_height;
				$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
				$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $starty);
			}
		} else {// ครอปจตุรัส
			if (($this->image_width >= $this->image_height) && ($this->image_width > $width)) {// รูปแนวนอนและภาพใหญ่กว่าครอป
				$newwidth = ($this->image_width/$this->image_height)*$height;
				$newheight = $height;
				$startx = ($startx == null ? (($width/2)-($newwidth/2)) : $startx);
				$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $starty);
			} elseif (($this->image_width < $this->image_height) && ($this->image_height > $height)) {// รูปแนวตั้ง และภาพสูงกว่าครอป.
				$newwidth = $width;
				$newheight = ($this->image_height/$this->image_width)*$newwidth;
				$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
				$starty = ($starty == null ? (($height/2)-($newheight/2)) : $starty);
			} else {
				$newwidth = $this->image_width;
				$newheight = $this->image_height;
				$startx = ($startx == null ?(($width/2) >= ($newwidth/2) ? ($width/2)-($newwidth/2) : "0") : $startx);
				$starty = ($starty == null ? (($height/2) >= ($newheight/2) ? ($height/2)-($newheight/2) : "0") : $starty);
			}
		}
		// start to resize or zoom
		$resizewidth = $newwidth;
		$resizeheight = $newheight;
		$resize = $this->resize_no_ratio($resizewidth, $resizeheight);
		$startx = ($startx == null ? "0" : $startx);
		$starty = ($starty == null ? "0" : $starty);
		//start to crop
		$image_read = $this->new_image;
		$new_image = imagecreatetruecolor($width, $height);
		$black = imagecolorallocate($new_image, 0, 0, 0);
		$white = imagecolorallocate($new_image, 255, 255, 255);
		$transwhite = imagecolorallocatealpha($new_image, 255, 255, 255, 127);// set color transparent white
		if ("1" == $this->image_type) {
			// gif image
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $transwhite);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} elseif ($this->image_type == "2") {
			// jpeg image
			imagefill($new_image, 0, 0, $white);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} elseif ($this->image_type == "3") {
			// png image
			imagefill($new_image, 0, 0, $transwhite);
			imagecolortransparent($new_image, $black);
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $image_read, $startx, $starty, 0, 0, $resizewidth, $resizeheight);
		} else {
			imagedestroy($image_read);
			imagedestroy($new_image);
			$this->destroy();
			return false;
		}
		// end crop process
		$this->image_read = $image_read;
		$this->new_height = $height;
		$this->new_image = $new_image;
		$this->new_width = $width;
		imagedestroy($image_read);
	}// zom_crop_fit
	
	/*----------------------------------------------------------------ส่วน modify----------------------------------------------------------------*/
	/*----------------------------------------------------------------ส่วน display----------------------------------------------------------------*/
	
	/**
	 *save($type="", $file_name = "")
	 *save image to file.
	 *$type is gif or jpg or png or nothing(same as image source)
	 *$file_name is full path to file name.
	*/
	public function save($type = "", $file_name = "") {
		if ($file_name != null) {
			$result = true;
			$type = strtolower($type);
			if ($type == null) {
				if ("1" == $this->image_type) {
					// gif image
					$result = imagegif($this->new_image, $file_name);
				} elseif ($this->image_type == "2") {
					// jpeg image
					$result = imagejpeg($this->new_image, $file_name, 100);
				} elseif ($this->image_type == "3") {
					// png image
					$result = imagepng($this->new_image, $file_name, 0);
				} else {
					$this->destroy();
					return false;
				}
			} else {
				if ($type == "gif") {
					if ($this->image_type == "3") {
						// in png out gif need special fill
						$img = imagecreatetruecolor($this->new_width, $this->new_height);// create canvas
						$white = imagecolorallocate($img, 255, 255, 255);// set color
						imagefill($img, 0, 0, $white);// fill canvas with color
						imagecopy($img, $this->new_image, 0, 0, 0, 0, $this->new_width, $this->new_height);
						unset($img, $white);
					}
					$result = imagegif($this->new_image, $file_name);
				} elseif ($type == "jpg") {
					if ($this->image_type == "3") {
						// in png out jpg need special fill
						$img = imagecreatetruecolor($this->new_width, $this->new_height);// create canvas
						$white = imagecolorallocate($img, 255, 255, 255);// set color
						imagefill($img, 0, 0, $white);// fill canvas with color
						imagecopy($img, $this->new_image, 0, 0, 0, 0, $this->new_width, $this->new_height);
						unset($img, $white);
					}
					$result = imagejpeg($this->new_image, $file_name, 100);
				} elseif ($type == "png") {
					$result = imagepng($this->new_image, $file_name, 0);
				} else {
					$this->destroy();
					return false;
				}
			}
			//@imagedestroy($this->image_read);
			@imagedestroy($this->new_image);
			if ($result == false) {
				$this->destroy();
				return false;
			} else {
				return true;
			}
		} else {
			$this->destroy();
			return false;
		}
	}// save
	
	/**
	 *show($type='')
	 *render image to browser
	 *$type is gif or jpg or png or nothing(same as image source)
	*/
	public function show($type='') {
		$type = strtolower($type);
		if ($type == null) {
			if ("1" == $this->image_type) {
				// gif image
				imagegif($this->new_image);
			} elseif ($this->image_type == "2") {
				// jpeg image
				imagejpeg($this->new_image, '', 100);
			} elseif ($this->image_type == "3") {
				// png image
				imagepng($this->new_image, '', 0);
			} else {
				$this->destroy();
				return false;
			}
		} else {
			if ($type == "gif") {
				$new_image = $this->new_image;
				if ($this->image_type == "3") {
					// in png out gif need special fill
					$img = imagecreatetruecolor($this->new_width, $this->new_height);// create canvas
					$white = imagecolorallocate($img, 255, 255, 255);// set color
					imagefill($img, 0, 0, $white);// fill canvas with color
					imagecopy($img, $this->new_image, 0, 0, 0, 0, $this->new_width, $this->new_height);
					$new_image = $img;
				}
				imagegif($new_image);
			} elseif ($type == "jpg") {
				$new_image = $this->new_image;
				if ($this->image_type == "3") {
					// in png out jpg need special fill
					$img = imagecreatetruecolor($this->new_width, $this->new_height);// create canvas
					$white = imagecolorallocate($img, 255, 255, 255);// set color
					imagefill($img, 0, 0, $white);// fill canvas with color
					imagecopy($img, $this->new_image, 0, 0, 0, 0, $this->new_width, $this->new_height);
					$new_image = $img;
				}
				imagejpeg($new_image, '', 100);
			} elseif ($type == "png") {
				imagepng($this->new_image, '', 0);
			} else {
				$this->destroy();
				return false;
			}
		}
		@imagedestroy($this->image_read);
		imagedestroy($this->new_image);
	}// show
	
	/*----------------------------------------------------------------ส่วน display----------------------------------------------------------------*/
	/*----------------------------------------------------------------ส่วน addon----------------------------------------------------------------*/
	
	/**
	 *watermark($watermarkfile, $posx='0', $posy='0')
	 *add watermark to exist image
	 ***you have to call resize or resize_no_ratio before this method.**
	 *$watermarkfile is full path to watermark image file (support gif, jpg, png)
	 *$posx is position x default is 0
	 *$posy is position y default is 0
	 */
	public function watermark($watermarkfile, $posx="0", $posy="0") {
		if (file_exists($watermarkfile)) {
			$wmsize = getimagesize($watermarkfile);
			if ($wmsize != false) {
				$width = $wmsize[0];
				$height = $wmsize[1];
				$imgtype_xxx = $wmsize[2];// convert from number(xxx) to ext by image_type_to_extension($imgtype_xxx)
				// value of xxx 1=gif/2=jpeg/3=png/4=swf/5=psd/6=bmp/7=tiff/8=tiff?/9=jpc/10=jp2/11=jpx/12=jb2/13=swf/14=iff/15=bmp/16=xbm/
				$mime = $wmsize['mime'];// eg. image/gif, image/jpeg
			}
			if ("1" == $imgtype_xxx) {
				// gif
				$watermark_read = imagecreatefromgif($watermarkfile);
				imagecopy($this->new_image, $watermark_read, $posx, $posy, 0, 0, $width, $height);
				//imagesavealpha($this->new_image, true);
			} elseif ($imgtype_xxx == "2") {
				// jpeg
				$watermark_read = imagecreatefromjpeg($watermarkfile);
				imagecopy($this->new_image, $watermark_read, $posx, $posy, 0, 0, $width, $height);
			} elseif ($imgtype_xxx == "3") {
				// png
				$watermark_read = imagecreatefrompng($watermarkfile);
				imagealphablending($this->new_image, true);// add this for transparent watermark thru image. if not add transparent from watermark can see thru background under image.
				imagecopy($this->new_image, $watermark_read, $posx, $posy, 0, 0, $width, $height);
			} else {
				$this->destroy();
				return false;
			}
		} else {
			$this->destroy();
			return false;
		}
		@imagedestroy($watermark_read);
		return $this->new_image;
	}// watermark
	
	/**
	 *watermark_easy($watermarkfile, $position="center")
	 *add watermark to exist image
	 ***you have to call resize or resize_no_ratio before this method**
	 *$watermarkfile is full path to watermark image file (support gif, jpg, png)
	 *$position is topleft, top, topright, left, center, right, bottomleft, bottom, bottomright
	*/
	public function watermark_easy($watermarkfile, $position="center") {
		if (file_exists($watermarkfile)) {
			// find watermark size
			$wmsize = getimagesize($watermarkfile);
			if ($wmsize != false) {
				$width = $wmsize[0];
				$height = $wmsize[1];
			}
			// end find watermark size
			if ($position == "topleft") {
				if (($this->new_width+5) > $width && ($this->new_height+5) > $height) {
					$posx = "5";
					$posy = "5";
				} else {
					$posx = "0";
					$posy = "0";
				}
			} elseif ($position == "top") {
				$center_of_image = round($this->new_width/2);// width
				$center_of_watermark = round($width/2);// width
				$posx = ($center_of_image-$center_of_watermark);
				if(($this->new_height) > ($height+5)) {
					$posy = "5";
				} else {
					$posy = "0";
				}
			} elseif ($position == "topright") {
				if ($this->new_width > ($width+5)) {
					$posx = $this->new_width-($width+5);
				} else {
					$posx = $this->new_width-$width;
				}
				if ($this->new_height > ($height+5)) {
					$posy = "5";
				} else {
					$posy = "0";
				}
			} elseif ($position == "left") {
				if ($this->new_width-($width+5) > "0") {
					$posx = "5";
				} else {
					$posx = "0";
				}
				$middle_of_image = round($this->new_height/2);// height
				$middle_of_watermark = round($height/2);// height
				$posy = ($middle_of_image-$middle_of_watermark);
			} elseif ($position == "center") {
				$center_of_image = round($this->new_width/2);// width
				$center_of_watermark = round($width/2);// width
				$posx = ($center_of_image-$center_of_watermark);
				$middle_of_image = round($this->new_height/2);// height
				$middle_of_watermark = round($height/2);// height
				$posy = ($middle_of_image-$middle_of_watermark);
			} elseif ($position == "right") {
				if ($this->new_width > ($width+5)) {
					$posx = $this->new_width-($width+5);
				} else {
					$posx = $this->new_width-$width;
				}
				$middle_of_image = round($this->new_height/2);// height
				$middle_of_watermark = round($height/2);// height
				$posy = ($middle_of_image-$middle_of_watermark);
			} elseif ($position == "bottomleft") {
				if ($this->new_width-($width+5) > "0") {
					$posx = "5";
				} else {
					$posx = "0";
				}
				if ($this->new_height-($height+5) > "0") {
					$posy = $this->new_height-($height+5);
				} else {
					$posy = $this->new_height-($height);
				}
			} elseif ($position == "bottom") {
				$center_of_image = round($this->new_width/2);// width
				$center_of_watermark = round($width/2);// width
				$posx = ($center_of_image-$center_of_watermark);
				if ($this->new_height-($height+5) > "0") {
					$posy = $this->new_height-($height+5);
				} else {
					$posy = $this->new_height-($height);
				}
			} elseif ($position == "bottomright") {
				if ($this->new_width > ($width+5)) {
					$posx = $this->new_width-($width+5);
				} else {
					$posx = $this->new_width-$width;
				}
				if ($this->new_height-($height+5) > "0") {
					$posy = $this->new_height-($height+5);
				} else {
					$posy = $this->new_height-($height);
				}
			} else {
				$this->destroy();
				return false;
			}
		} else {
			$this->destroy();
			return false;
		}
		return $this->watermark($watermarkfile, $posx, $posy);
	}// watermark_easy

	function watermark_okvg($text = '') {
		$wmtxt_width = 500;
		$wmtxt_height = 20;
		$wm_img_height = 30;// watermark image height
		if ($this->new_width < $wmtxt_width || $this->new_height < $wmtxt_height) {return $this->new_image;}
		$im = imagecreatetruecolor($wmtxt_width, $wmtxt_height);
		imagealphablending($im, false);
		imagesavealpha($im, true);
		$font = dirname(__FILE__).'/tahoma.ttf';
		// color
		$black = imagecolorallocate($im, 0, 0, 0);
		$white = imagecolorallocate($im, 255, 255, 255);
		$transwhite = imagecolorallocatealpha($im, 255, 255, 255, 127);// set color transparent white
		$transwhitetxt = imagecolorallocatealpha($im, 255, 255, 255, 80);
		//set text
		imagefill($im, 0, 0, $transwhite);
		imagettftext($im, 10, 0, 1, $wmtxt_height-5, $transwhitetxt, $font, $text);
		imagecolortransparent($im, $transwhite);
		// copy text to image.
		imagecopy($this->new_image, $im, 5, ($this->new_height-$wmtxt_height), 0, 0, $wmtxt_width, $wmtxt_height);
		imagedestroy($im);
	}// watermark_okvg
	
	/*----------------------------------------------------------------ส่วน addon----------------------------------------------------------------*/
	
}

?>