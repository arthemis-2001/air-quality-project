<?php

function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}