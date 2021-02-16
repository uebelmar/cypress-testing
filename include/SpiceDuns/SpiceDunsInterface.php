<?php
namespace SpiceCRM\includes\SpiceDuns;

interface SpiceDunsInterface
{


    public function sendRequest($params = []);

    public function handleResponse($response);


}
