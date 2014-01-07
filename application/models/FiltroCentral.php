<?php
class FiltroCentral {
  public static function removeMascara($str) {
		return preg_replace('/[^a-zA-Z0-9]/', '', $str);
  }
  
}