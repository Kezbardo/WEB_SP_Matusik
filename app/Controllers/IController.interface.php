<?php
namespace app\Controllers;

interface IController {
    /**
     * Printing of the correct page
     * @param string $pageTitle Page title.
     * @return array
     */
    public function show(string $pageTitle):array;
}