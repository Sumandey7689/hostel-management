<?php
class Helper
{
    public function getFormatedDate($date)
    {
        return Date('d-m-Y', strtotime($date));
    }
}
