<?php

if (! function_exists('formatDateTime')) {
  function formatDateTime($dateTime) {
    return date('d M, Y', strtotime($dateTime));
  }
}
