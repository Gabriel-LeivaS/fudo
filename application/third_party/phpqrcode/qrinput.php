<?php
class QRinput {
    public $data = array();
    public function append($mode, $size, $data) { $this->data[] = $data; }
}
